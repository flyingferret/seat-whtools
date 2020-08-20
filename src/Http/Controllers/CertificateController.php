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
use FlyingFerret\Seat\WHTools\Models\CharacterCertificate;
use Seat\Web\Http\Controllers\Controller;
use FlyingFerret\Seat\WHTools\Models\Sde\DgmTypeAttributes;
use FlyingFerret\Seat\WHTools\Models\Sde\InvType;
use FlyingFerret\Seat\WHTools\Models\Sde\InvGroups;
use FlyingFerret\Seat\WHTools\Validation\CertificateValidation;

use Seat\Eveapi\Models\Character\CharacterInfo;
use Seat\Eveapi\Models\Character\CharacterAffiliation ;
use Seat\Eveapi\Models\Corporation\CorporationInfo;
use FlyingFerret\Seat\WHTools\Jobs\CertificatesSync;

/**
 * Class HomeController
 * @package Author\Seat\YourPackage\Http\Controllers
 */
class CertificateController extends Controller
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
        return view('whtools::skillchecker', compact('allSkills'));
    }

    public function getCertificatesView()
    {
        $certificates = Certificate::all();
        return view('whtools::index', compact('certificates'));
    }

    public function saveCertificate(CertificateValidation $request)
    {
        $cert = new Certificate();

        if ($request->certificateID > 0) {
            $cert = Certificate::findorfail($request->certificateID);
            CertificateSkill::where('certID', $request->certificateID)->delete();
        }

        $cert->name = $request->certificateName;
        $cert->description = "Nil";
        $cert->save();

        foreach ($request->selectedSkills as $skillCode) {
            $skill = new CertificateSkill();
            $skillCode = (string)$skillCode;
            $skillID = substr($skillCode, 0, strlen($skillCode) - 2);
            $reqLvl = substr($skillCode, strlen($skillCode) - 2, 1);
            $certLvl = substr($skillCode, strlen($skillCode) - 1, 1);
            $skill->skillID = $skillID;
            $skill->requiredLvl = $reqLvl;
            $skill->certRank = $certLvl;
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

        foreach ($cert->skills()->get() as $certSkill) {
            array_push($certSkills, [
                'skillID' => $certSkill->skillID,
                'skillName' => $this->getSkillName($certSkill->skillID),
                'requiredLvl' => $certSkill->requiredLvl,
                'certRank' => $certSkill->certRank
            ]);
        }

        return [
            'cert' => $cert,
            'allSkills' => $allSkills,
            'certSkills' => $certSkills
        ];
    }

    public function delCertificate($id)
    {
        $cert = Certificate::findOrFail($id);
        $skills = $cert->skills()->delete();
        CharacterCertificate::where('certID', $cert->certID)->delete();
        $cert->delete();

        return "Success";
    }

    public function getAllSkills()
    {
        $skillIDs = DgmTypeAttributes::where('attributeID', '275')->get();
        $skills = [];
        foreach ($skillIDs as $skillID) {
            $res1 = InvType::where('typeID', $skillID['typeID'])->first();
            $res2 = InvGroups::where('groupID', $res1['groupID'])->first();

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
        return $res1->typeName;
    }

    public function getCharacterSkills($characterID)
    {
        return CharacterInfo::findOrFail($characterID)->skills()->get();
    }

    public function getCharacterCerts($characterID)
    {
        $charCerts = [];
        $characters = [];
        if (auth()->user()->can('whtools.certchecker')) {
            $characters = CorporationInfo::find(auth()->user()->main_character->affiliation->corporation_id)->characters;
        } else {
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
        foreach ($allCerts as $cert) {
            $certSkills = $cert->skills()->orderBy('certRank', 'asc')->get();
            $certRank = 5;
            foreach ($certSkills as $certSkill) {
                $charSkill = CharacterInfo::findOrFail($characterID)->skills()->where('skill_id', $certSkill->skillID)->first();
                if ((empty($charSkill) or $charSkill->trained_skill_level < $certSkill->requiredLvl) and $certRank >= $certSkill->requiredLvl) {
                    $certRank = $certSkill->certRank - 1;
                }
            }
            array_push($charCerts, ['characterCert' => $cert, 'certRank' => $certRank]);
        }
        array_push($charCerts, ['characters' => $characters]);
        return $charCerts;
    }

// Returns corporation certificates in character batches
    public function getCorporationCertificates($corporationID)
    {
        $corp = CorporationInfo::findOrFail($corporationID);
        $corpCerts = collect();
        foreach ($corp->characters()->get() as $character) {
            //filter out null records
            if (CharacterCertificate::where('character_id', $character->character_id)->first()) {
                $corpCerts->push(CharacterCertificate::where('character_id', $character->character_id)->get());
            }
        }
        return $corpCerts;
    }

    public function getCorporationCertificateCoverageChartData($corp_id)
    {
        $corp = CorporationInfo::findOrFail($corp_id);
        $corpCerts = collect();
        $characters = $corp->characters;
        foreach ($characters as $character) {
            //filter out null records
            if (CharacterCertificate::where('character_id', $character->character_id)->first()) {
                foreach (CharacterCertificate::where('character_id', $character->character_id)->get() as $cert) {
                    $corpCerts->push($cert);
                }
            }
        }

        $labels = [];
        $data = [];

        $certificates = Certificate::all();
        foreach ($certificates as $certificate) {
            array_push($labels, $certificate->name);
            $passCount = 0;
            foreach ($corpCerts as $corpCert) {
                if ($corpCert->rank == 5 and $corpCert->certID == $certificate->certID) {
                    $passCount = $passCount + 1;
                }
            }
            array_push($data, ($passCount / $characters->count() * 100));
        }


        return response()->json([
            'labels' => $labels, // certNames
            'datasets' => [
                [
                    'label' => 'Certificates',
                    'data' => $data,
                    'fill' => true,
                    'backgroundColor' => 'rgba(60,141,188,0.3)',
                    'borderColor' => '#3c8dbc',
                    'pointBackgroundColor' => '#3c8dbc',
                    'pointBorderColor' => '#fff',
                ],
            ],
        ]);
    }
}
