<?php
/**
 * AutoRedirect pre-install script
 *
 * Copyright 2011 Benjamin Vauchel <contact@omycode.fr>
 * @author Benjamin Vauchel <contact@omycode.fr>
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
 * Description: Example validator checks for existence of getResources
 * @package autoredirect
 * @subpackage build
 */
/**
 * @package autoredirect
 * Validators execute before the package is installed. If they return
 * false, the package install is aborted. This example checks for
 * the installation of getResources and aborts the install if
 * it is not found.
 */

/* The $modx object is not available here. In its place we
 * use $object->xpdo
 */
$modx =& $object->xpdo;


$modx->log(xPDO::LOG_LEVEL_INFO,'Running PHP Validator.');
switch($options[xPDOTransport::PACKAGE_ACTION]) {
    case xPDOTransport::ACTION_INSTALL:

        $modx->log(xPDO::LOG_LEVEL_INFO,'Checking for installed Redirector component ');
        $success = true;
        /* Check for Redirector */
        $redirector = $modx->getObject('modPlugin',array('name'=>'Redirector'));
        if ($redirector) {
            $modx->log(xPDO::LOG_LEVEL_INFO,'Redirector found - install will continue');
            unset($redirector);
        } else {
            $modx->log(xPDO::LOG_LEVEL_ERROR,'This package requires the Redirector package. Please install it and reinstall AutoRedirect');
            $success = false;
        }

        break;
   /* These cases must return true or the upgrade/uninstall will be cancelled */
   case xPDOTransport::ACTION_UPGRADE:
        $success = true;
        break;

    case xPDOTransport::ACTION_UNINSTALL:
        $success = true;
        break;
}

return $success;