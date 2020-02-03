<?php
/*
This file is part of SeAT

Copyright (C) 2015, 2017  Leon Jacobs

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License along
with this program; if not, write to the Free Software Foundation, Inc.,
51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/

namespace FlyingFerret\Seat\WHTools\Http\Controllers;

use FlyingFerret\Seat\WHTools\Models\Certificate;
use FlyingFerret\Seat\WHTools\Models\CertificateSkill;
use Seat\Web\Http\Controllers\Controller;
use FlyingFerret\Seat\WHTools\Models\Sde\DgmTypeAttributes;
use FlyingFerret\Seat\WHTools\Models\Sde\InvType;
use FlyingFerret\Seat\WHTools\Models\Sde\InvGroups;
use FlyingFerret\Seat\WHTools\Validation\CertificateValidation;

use Seat\Eveapi\Models\Character\CharacterInfo;
use Seat\Eveapi\Models\Corporation\CorporationInfo;

/**
 * Class HomeController
 * @package Author\Seat\YourPackage\Http\Controllers
 */
class SkillCheckerController extends Controller
{

    /**
     * @return \Illuminate\View\View
     */
    public function getHome()
    {
        return view('whtools::whtools');
    }
    public function getSkillCheckerView()
    {
        $allSkills = $this->getAllSkills();
        return view('whtools::skillchecker',compact('allSkills'));
    }
    public function getCertificatesView()
    {
        $certificates = Certificate::all();
        return view('whtools::index',compact('certificates'));
    }
    public function saveCertificate(CertificateValidation $request)
    {
        $cert = new Certificate();

        if ($request->certificateID > 0) {
            $cert = Certificate::findorfail($request->certificateID);
            CertificateSkill::where('certID',$request->certificateID )->delete();
        }

        $cert->name = $request->certificateName;
        $cert->description = "Nil";
        $cert->save();

        foreach ($request->selectedSkills as $skillCode) {
            $skill = new CertificateSkill();
            $skillCode = (string)$skillCode;
            $skillID = substr ($skillCode,0,strlen($skillCode)-2);
            $reqLvl = substr ($skillCode,strlen($skillCode)-2,1);
            $certLvl = substr ($skillCode,strlen($skillCode)-1,1);
            $skill->skillID  = $skillID;
            $skill->requiredLvl  = $reqLvl;
            $skill->certRank  = $certLvl;
            $cert->skills()->save($skill);
        }

        return redirect()->route('whtools.certificates');
    }

    public function getCertificateByID($id)
    {
        $skill_list = [];

        $doctrine = Certificate::find($id);
        $skills = $doctrine->skills()->get();

        foreach ($skills as $skill) {

            array_push($skill_list, [
                'skillID' => $skill->skillID,
                'skillName' => $this->getSkillName($skill->skillID),
                'reqLvl' => $skill->requiredLvl,
                'certRank' => $skill->certRank,
            ]);
        }
        return $skill_list;
    }

    public function getCertEdit($certID)
    {
        $cert = Certificate::findorfail($certID);
        $allSkills = $this->getAllSkills();
        $certSkills = [];

        foreach ($cert->skills()->get() as $certSkill){
            array_push($certSkills,[
                'skillID'=> $certSkill->skillID,
                'skillName'=> $this->getSkillName($certSkill->skillID),
                'requiredLvl'=> $certSkill->requiredLvl,
                'certRank'=> $certSkill->certRank
                ]);
        }

        return[
            'cert'=>$cert,
            'allSkills'=>$allSkills,
            'certSkills'=>$certSkills
        ];
    }

    public function delCertificate($id)
    {
        $cert = Certificate::findOrFail($id)->first();
        $skills = $cert->skills()->delete();
        $cert->delete();

        return "Success";
    }

    public function getAllSkills()
    {
        $skillIDs = DgmTypeAttributes::where('attributeID','275')->get();
        $skills = [];
        foreach ($skillIDs as $skillID) {
            $res1 = InvType::where('typeID', $skillID['typeID'])->first();
            $res2 =  InvGroups::where('groupID',$res1['groupID'])->first();

            array_push($skills, [
                'typeID' => $skillID['typeID'],
                'typeName' => $res1->typeName,
                'groupID' => $res1->groupID,
                'groupName' => $res2->groupName
            ]);
        }

        ksort($skills);

        return $skills;
    }
    public function getSkillName($id)
    {
        $res1 = InvType::where('typeID', $id)->firstOrFail();
        return  $res1->typeName;
    }

    public function getCharacterSkills($characterID)
    {
        return CharacterInfo::findOrFail($characterID)->skills()->get();
    }
    public  function getCharacterCerts($characterID){
        $charCerts = [];
        $characters =[];
        if (auth()->user()->has('whtools.certchecker', false)){
            $characters =  CharacterInfo::where('corporation_id',auth()->user()->character->corporation_id)->get();
        }else {
            $characterIds = auth()->user()->associatedCharacterIds();
            foreach ($characterIds as $characterId) {
                $character = CharacterInfo::where('character_id', $characterId)->first();

                // Sometimes you'll have character_id associated, but the update job hasn't run.
                if ($character != null) {
                    array_push($characters, $character);
                }
            }
        }

        $allCerts = Certificate::get();
        foreach ($allCerts as $cert){
            $certSkills = $cert->skills()->get();
            $certRank = 5;
            foreach ($certSkills as $certSkill){
                $charSkill = CharacterInfo::findOrFail($characterID)->skills()->where('skill_id',$certSkill->skillID)->first();
                if(isset($charSkill) and $charSkill->trained_skill_level < $certSkill->requiredLvl){
                    $certRank = $certSkill->certRank - 1;
                }
            }
            array_push($charCerts,['characterCert'=>$cert,'certRank'=>$certRank]);
        }
        array_push($charCerts,['characters'=>$characters]);
        return $charCerts;
    }

    public function getCorporationCertificates($corporationID){
        $corporationCertificates = [];
        $characters = CorporationInfo::where('corporation_id',$corporationID)->firstOrFail()->characters()->get();
        foreach ($characters as $character){
            $data = [];
            array_push($data,['Character'=> $character]);
            array_push($data,['CharacterCerts'=> $this->getCharacterCerts($character->character_id)]);
            array_push($corporationCertificates,['data'=>$data]);
            $data = [];
        }
        return json_encode($corporationCertificates);
    }
}
