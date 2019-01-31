<?php

/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

/* * ***************************Includes********************************* */
require_once __DIR__  . '/../../../../core/php/core.inc.php';
include __DIR__  . '/plexAPI.class.php';


class plex extends eqLogic  {
    /*     * *************************Attributs****************************** */



    /* 
     * ***********************Methode static*************************** 
     * */

    /*
     * Fonction exécutée automatiquement toutes les minutes par Jeedom
      public static function cron() {

      }
     */


    /*
     * Fonction exécutée automatiquement toutes les heures par Jeedom*/
      public static function cronHourly() 
      {
            /*    if ($_eqLogic_id == null) { // La fonction n’a pas d’argument donc on recherche tous les équipements du plugin
                    $eqLogics = self::byType('plex', true);
                } else {// La fonction a l’argument id(unique) d’un équipement(eqLogic)
                    $eqLogics = array(self::byId($_eqLogic_id));
                }		  
            
                foreach ($eqLogics as $plex) {
                    if ($plex->getIsEnable() == 1) {//vérifie que l'équipement est acitf
                        $cmd = $plex->getCmd(null, 'refresh');//retourne la commande "refresh si elle exxiste
                        if (!is_object($cmd)) {//Si la commande n'existe pas
                        continue; //continue la boucle
                        }
                        $cmd->execCmd(); // la commande existe on la lance
                    }
                }*/
      }
     

    /*
     * Fonction exécutée automatiquement tous les jours par Jeedom
      public static function cronDaily() {

      }
     */



    /*     * *********************Méthodes d'instance************************* */

    public function preInsert() {
        
    }

    public function postInsert() {
        
    }

    public function preSave() {
		$this->setDisplay("width","400px");
		// Sauvegarder et regarder le rendu
      /*  $this->setDisplay("width","400px");
        $this->setDisplay("showNameOndashboard",0);*/
    }

    public function postSave() {
        $info = $this->getCmd(null, 'connect');
		//$info->setTemplate('dashboard','default'); 
		if (!is_object($info)) {
			$info = new plexCmd();
			$info->setName(__('Ajout Récents', __FILE__));
		}
		$info->setLogicalId('connect');
		$info->setEqLogic_id($this->getId());
		$info->setTemplate('dashboard', 'plex_caroussel');

		$info->setType('info');
		$info->setSubType('string');

		$info->save();	
		
		$refresh = $this->getCmd(null, 'refresh');
		if (!is_object($refresh)) {
			$refresh = new plexCmd();
			$refresh->setName(__('Rafraichir', __FILE__));
		}
		$refresh->setEqLogic_id($this->getId());
		$refresh->setLogicalId('refresh');
		$refresh->setType('action');
		$refresh->setSubType('other');
		$refresh->save();    
    }

    public function preUpdate() {
        
    }

    public function postUpdate() {
   //     self::cronHourly($this->getId());// lance la fonction cronHourly avec l’id de l’eqLogic

    }

    public function preRemove() {
        
    }

    public function postRemove() {
        
    }

    /*
     * Non obligatoire mais permet de modifier l'affichage du widget si vous en avez besoin
    */  
	// public function toHtml($_version = 'dashboard') {
		// if ($_version == "dashboard")
			// return
    // }
     

    /*
     * Non obligatoire mais ca permet de déclencher une action après modification de variable de configuration
    public static function postConfig_<Variable>() {
    }
     */

    /*
     * Non obligatoire mais ca permet de déclencher une action avant modification de variable de configuration
    public static function preConfig_<Variable>() {
    }
     */

     public function recentlyAdded()
     {
         // $domain = $this->getConfiguration("domain");
         $ip = $this->getConfiguration("domain");
         $port = $this->getConfiguration("port");
         $username = $this->getConfiguration("username");
         $password = $this->getConfiguration("password");
	
		 $plexServer = array(
			'scheme' => 'http',
			'domain' => $ip,
			'port' => $port,
			'token' => '',
			'username' => $username,
			'password' => $password
		);
	
		$plexScriptInfo = array(
			'script_name' => 'Plex PHP API class',
			'script_version' => 'v0.1 (beta)',
			'script_description' => 'PHP class for the plex web API',
			'script_guid' => ''
		);
		$plexScriptInfo["script_guid"] = plexAPI::generateGUID();
		$plex = new plexAPI($plexServer,$plexScriptInfo);
		$elements = ($plex->getRecentlyAdded('movie'));
		
		foreach($elements["items"] as $item)
		{	
			$lignes .= "<img width='auto' height='300px' src='".$plexServer["scheme"]."://".$plexServer["domain"].":".$plexServer["port"]."".($item["thumb"])."?X-Plex-Token=".$plex->getToken()."' />";
		}
		
		return $lignes;
     }

   /* public function randomVdm() {
        $type = $this->getConfiguration("type");
		if($type == "") { //si le paramètre est vide ou n’existe pas
			$type = "aleatoire"; //on prends le type aleatoire
		}		
		$url = "http://www.viedemerde.fr/" .$type  ;
		//$url = "http://www.viedemerde.fr/aleatoire";
		$data = file_get_contents($url);
		@$dom = new DOMDocument();
		libxml_use_internal_errors(true);
		$dom->loadHTML($data);
		libxml_use_internal_errors(false);
		$xpath = new DOMXPath($dom);
		$divs = $xpath->query('//article[@class="art-panel col-xs-12"]//div[@class="panel-content"]//p//a');
		return $divs[0]->nodeValue ;
	}*/

    /*     * **********************Getteur Setteur*************************** */
}

class plexCmd extends cmd {
    /*     * *************************Attributs****************************** */


    /*     * ***********************Methode static*************************** */


    /*     * *********************Methode d'instance************************* */

    /*
     * Non obligatoire permet de demander de ne pas supprimer les commandes même si elles ne sont pas dans la nouvelle configuration de l'équipement envoyé en JS
      public function dontRemoveCmd() {
      return true;
      }
     */

    public function execute($_options = array()) 
    {
		$originalLogicalId = $this->getLogicalId();
		
		$eqlogic = $this->getEqLogic(); //récupère l'éqlogic de la commande $this
		
		
		switch ($this->getLogicalId()) {	//vérifie le logicalid de la commande 			
			case 'refresh': // LogicalId de la commande rafraîchir que l’on a créé dans la méthode Postsave de la classe plex . 
				$info = $eqlogic->recentlyAdded(); 	//On lance la fonction randomVdm() pour récupérer une plex et on la stocke dans la variable $info
				//$elements = $eqlogic->checkAndUpdateCmd('connect', $info); // on met à jour la commande avec le LogicalId "story"  de l'eqlogic 
				// ($info);
				$_options['data'] = $info;
				break;
		}
    }

    

    /*     * **********************Getteur Setteur*************************** */
}


