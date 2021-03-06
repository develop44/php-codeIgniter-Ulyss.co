<?php

/* @var array $mentors */
/* @var StdClass $mentor */
/* @var int $mentors_count */


/**
 * Class BusinessSearch
 */
class BusinessSearch extends Business
{
    private $_BusinessUser, $_entrepriseId, $_Entreprise;

    public function __construct($params = [])
    {
        parent::__construct();

        $this->_BusinessUser = $params['BusinessUser'];
        $this->_entrepriseId = $params['entrepriseId'];
        $this->_Entreprise   = $this->getContext()->ModelEntreprise->getById($this->_entrepriseId);
    }


    /**
     * @return BusinessUser
     */
    public function getBusinessUser()
    {
        return $this->_BusinessUser;
    }

    /**
     * @return int
     */
    public function getEntrepriseId()
    {
        return $this->_entrepriseId;
    }

    /**
     * @return StdClass
     */
    public function getEntreprise()
    {
        return $this->_Entreprise;
    }

    /**
     *
     */
    public function sendSearch()
    {
        if ($this->getBusinessUser()->isBasicProfil()) {
            $this->sendSearchBasicProfile();
        } else {
            if ($this->getBusinessUser()->isFullProfile()) {
                $this->sendSearchFullProfile();
            }
        }
    }

    /**
     * @return array
     */
    public function sendSearchBasicProfile()
    {
        $ModelSearch = new ModelSearch();
        if ($this->getEntreprise() == null) {
            return [];
        }

        return $ModelSearch->getSearchBasicProfile($this->getBusinessUser(), $this->getEntreprise());
    }


    /**
     * @return array
     */
    public function sendSearchByUserExpSecteur()
    {
        $ModelSearch = new ModelSearch();
        if ($this->getBusinessUser()->getProfileTalent()->getLastExperience()->secteur_id == null) {
            return [];
        }

        return $ModelSearch->getSearchSecteurProfil($this->getBusinessUser());
    }

    public function sendSearchFullProfile()
    {
        $ModelSearch = new ModelSearch();
        if ($this->getEntreprise() == null) {
            return [];
        }


        return $ModelSearch->getSearchFullProfile($this->getBusinessUser(), $this->getEntreprise());
    }

    public function getResultBasicProfile()
    {
        $Result = $this->sendSearchBasicProfile();
        $return = [];

        if (is_array($Result) && count($Result) > 0) {
            foreach ($Result as $mentor) {

                /* Si m??me entreprise */
                if ($mentor->id_entreprise == $this->getEntrepriseId()) {
                    /* Entreprise actuelle */
                    if ($mentor->date_fin == '9999-12-31' || $mentor->date_fin == '0000-00-00') {
                        $return[0][] = $mentor;
                    } else {
                        $return[10][] = $mentor;
                    }
                } /* Meme secteur d'activite pour l'entrprise selectionn??e */
                else {
                    /* Job en cours */
                    if ($mentor->date_fin == '9999-12-31' || $mentor->date_fin == '0000-00-00') {
                        $return[20][] = $mentor;
                    } else {
                        $return[30][] = $mentor;
                    }
                }
            }
        }
        ksort($return);

        return $return;
    }

    public function getResultFullProfile()
    {
        $BusinessTalent = $this->getBusinessUser()->getProfileTalent();

        $LastExperience = $BusinessTalent->getLastExperience();

        if ($LastExperience == null) {
            return $this->getResultBasicProfile();
        }

        $Result = $this->sendSearchFullProfile();

        $return = [];

        if (is_array($Result) && count($Result) > 0) {
            foreach ($Result as $mentor) {

                /* Si m??me entreprise */
                if ($mentor->id_entreprise == $this->getEntrepriseId()) {
                    /* Si le job est en cours */
                    if ($mentor->date_fin == '9999-12-31' || $mentor->date_fin == '0000-00-00') {
                        /* Entreprise / m??me d??partement / m??me fonction */
                        if ($mentor->id_departement == $LastExperience->departement_id) {
                            $return[0][] = $mentor;
                        } /* Entreprise / m??me d??partement  */
                        else {
                            if ($mentor->id_departement == $LastExperience->departement_id) {
                                $return[10][] = $mentor;
                            } /* Entreprise / pas le m??me d??partement */
                            else {
                                $return[20][] = $mentor;
                            }
                        }
                    } else {
                        /* Entreprise dans le pass?? / m??me d??partement / m??me fonction */
                        if ($mentor->id_departement == $LastExperience->departement_id) {
                            $return[30][] = $mentor;
                        } /* Entreprise dans le pass?? / m??me d??partement */
                        else {
                            if ($mentor->id_departement == $LastExperience->departement_id) {
                                $return[40][] = $mentor;
                            } /* Entreprise dans le pass?? / pas le m??me d??partement */
                            else {
                                $return[50][] = $mentor;
                            }
                        }
                    }
                } /* Pas la m??me entreprise - On controle le m??me secteur d'activit?? */
                else {
                    if ($mentor->filter_secteur_id == $this->getEntreprise()->secteur_id) {
                        /* Secteur d'activit?? de l'entreprise renseign??e / m??me d??partement / m??me fonction  */
                        if ($mentor->id_departement == $LastExperience->departement_id) {
                            $return[60][] = $mentor;
                        } /* Entreprise dans le pass?? / m??me d??partement */
                        else {
                            if ($mentor->id_departement == $LastExperience->departement_id) {
                                $return[70][] = $mentor;
                            } /* Entreprise dans le pass?? / pas le m??me d??partement */
                            else {
                                $return[80][] = $mentor;
                            }
                        }
                    } /* Pas la m??me entreprise - On controle le m??me secteur d'activit?? */
                    else {
                        if ($mentor->filter_secteur_id == $LastExperience->secteur_id) {
                            /* Secteur d'activit?? de l'entreprise renseign??e / m??me d??partement / m??me fonction  */
                            if ($mentor->id_departement == $LastExperience->departement_id) {
                                $return[90][] = $mentor;
                            } /* Entreprise dans le pass?? / m??me d??partement */
                            else {
                                if ($mentor->id_departement == $LastExperience->departement_id) {
                                    $return[100][] = $mentor;
                                } /* Entreprise dans le pass?? / pas le m??me d??partement */
                                else {
                                    $return[110][] = $mentor;
                                }
                            }
                        } /* Aucune condition respect??e */
                        else {
                            $return[120][] = $mentor;
                        }
                    }
                }
            }


        } elseif (count($this->sendSearchByUserExpSecteur()) > 0) {

            //recuperation de tout le montor du meme departement
            $Result = $this->sendSearchByUserExpSecteur();


            $departement = $this->getBusinessUser()->getProfileTalent()->getLastExperience()->departement_id;
            //Filtrage :
            foreach ($Result as $mentor) {
                //si il y a 0 mentor dans la recherche normal alor en affiche :

                $return[100][] = $mentor;
            }
        }
        ksort($return);

        return $return;
    }
}
