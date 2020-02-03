<?php


namespace FlyingFerret\Seat\WHTools\Jobs;

use FlyingFerret\Seat\WHTools\Models\Certificate;
use FlyingFerret\Seat\WHTools\Models\CharacterCertificate;
use Seat\Eveapi\Models\Corporation\CorporationInfo;


class CertificatesSync extends WHToolsJobBase
{
    /**
     * @var array
     */
    protected $tags = ['sync'];

    /**
     * @var int
     */
    public $tries = 1;

    /**
     * @var \Seat\Eveapi\Models\Corporation\CorporationInfo
     */
    private $corporation;

    /**
     * ConversationOrchestrator constructor.
     *
     * @param \Seat\Eveapi\Models\Corporation\CorporationInfo $corp
     */
    public function __construct(CorporationInfo $corp)
    {
        $this->corporation = $corp;
    }

    public function handle()
    {
        $characters = $this->corporation->characters()->get();
        $allCerts = Certificate::get();

        foreach($characters as $character){
            foreach($allCerts as $cert){

                $certSkills = $cert->skills()->get();
                $certRank = 5;
                foreach ($certSkills as $certSkill){
                    $charSkill = $character->skills()->where('skill_id',$certSkill->skillID)->first();
                    if(isset($charSkill) and $charSkill->trained_skill_level < $certSkill->requiredLvl){
                        $certRank = $certSkill->certRank - 1;
                    }
                }

                CharacterCertificate::updateOrCreate(
                    ['character_id'=>$character->character_id,'certID'=>$cert->name],
                    ['character_name'=>$character->name,'cert_name'=>$cert->name,'rank'=>$certRank]
                );
            }
        }

    }

    public function onFail($exception)
    {

        report($exception);

    }

    public function onFinish()
    {

    }
}
