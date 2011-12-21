<?php
/**
 * AutoRedirect autoredirect plugin
 *
 * Copyright 2011 Benjamin Vauchel <contact@omycode.fr>
 *
 * @author Benjamin Vauchel <contact@omycode.fr>
 * @version Version 1.0.0 Beta-1
 * 12/15/11
 *
 * AutoRedirect is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation; either version 2 of the License, or (at your option) any
 * later version.
 *
 * AutoRedirect is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * AutoRedirect; if not, write to the Free Software Foundation, Inc., 59 Temple
 * Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @package autoredirect
 */

/**
 * MODx AutoRedirect autoredirect plugin
 *
 * Description: AutoRedirect is a plugin for MODx Revolution which generate 301 redirections for resources whose alias was updated
 * WARNING : This plugin required the Redirector package to work
 * Events: OnBeforeDocFormSave, OnEmptyTrash
 *
 * @package autoredirect
 *
 */

$eventName = $modx->event->name;
switch($eventName) 
{
  // Création des redirections lors de la mise à jour d'une ressource
  case 'OnBeforeDocFormSave':
    
    if($mode == 'upd')
    {
      // On récupère l'ancien alias …
      $oldResource = $modx->getObject('modResource',$resource->get('id'));
      $oldAlias = $oldResource->get('alias');
    
      // … et le nouveau
      $newAlias = $_REQUEST['alias'];
    
      // Si l'alias a changé, on créé les redirections
      if(!empty($oldAlias) && $oldAlias != $newAlias)
      {
	// On charge le service Redirector
	$corePath =  $modx->getOption('redirector.core_path',$scriptProperties,$modx->getOption('core_path').'components/redirector/');
	$redirector = $modx->getService('redirector','Redirector',$corePath.'model/redirector/',$scriptProperties);
	if (!($redirector instanceof Redirector)) return '';
	
	// On spécifie le contexte pour créer des liens relatifs avec makeUrl
	$modx->switchContext('web');
	
	// On créé la redirection pour la ressource en cours …
	$values = '("'.$modx->makeUrl($resource->get('id')).'","[[~'.$resource->get('id').']]", '.($resource->get('deleted') ? 0 : 1).')';
	
	//  … mais aussi pour ses ressources filles
	$childrenIds = $modx->getChildIds($resource->get('id'), 5);
	foreach($childrenIds as $childId)
	{
	  $childResource = $modx->getObject('modResource', $childId);
	  
	  if(is_object($childResource))
	  {
	    $values .= ',("'.$modx->makeUrl($childResource->get('id')).'","[[~'.$childResource->get('id').']]", '.($childResource->get('deleted') ? 0 : 1).')';
	  }
	}
	
	// On utilise ici une syntaxe MySQL et non xPDO afin de gérer le ON DUPLICATE KEY
	$tableName = $modx->getTableName('modRedirect');
	$result = $modx->query("INSERT INTO {$tableName} (pattern,target,active) VALUES {$values} ON DUPLICATE KEY UPDATE target=VALUES(target)");
	if($result == false)
	{
	  $modx->log(modX::LOG_LEVEL_DEBUG, 'Redirects for resource '.$resource->get('id').' and childs havent been generated');
	}
      }
    }

  break;
	
  // Suppression des redirections inutiles lors de la purge des documents supprimés 
  case 'OnEmptyTrash':

    // On charge le service Redirector
    $corePath =  $modx->getOption('redirector.core_path',$scriptProperties,$modx->getOption('core_path').'components/redirector/');
    $redirector = $modx->getService('redirector','Redirector',$corePath.'model/redirector/',$scriptProperties);
    if (!($redirector instanceof Redirector)) return '';
	  
    // On détermine la liste des redirections à supprimer à partir 
    // de la liste des ids des ressources supprimées
    $targets = '';
    foreach($ids as $id)
    {
      if($targets != '')
      {
      	$targets .= ',';
      }
      $targets .= '"[[~'.$id.']]"';
    }
  
    if($targets != '')
    {
      // On utilise ici une syntaxe MySQL et non xPDO afin de gérer la suppression par lot
      $tableName = $modx->getTableName('modRedirect');
      $result = $modx->query("DELETE FROM {$tableName} WHERE target IN ({$targets})");
      if($result == false)
      {
      	$modx->log(modX::LOG_LEVEL_DEBUG, 'Redirects for deleted resources havent been deleted');
      } 
    }
  break;
  
}