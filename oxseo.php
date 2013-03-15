<?php
/**
 *    This file is part of OXID eShop Community Edition.
 *
 *    OXID eShop Community Edition is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    OXID eShop Community Edition is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @package   main
 * @copyright (C) OXID eSales AG 2003-2010
 * @version OXID eShop CE
 * @version   SVN: $Id: oxseo.php 26071 2010-02-25 15:12:55Z sarunas $
 */

// mod_rewrite check
if ( isset( $_REQUEST['mod_rewrite_module_is'] ) ) {
    die( "mod_rewrite_".$_REQUEST['mod_rewrite_module_is'] );
}

/**
 * Detects serchengine URLs
 *
 * @return bool true
 */
function isSearchEngineUrl()
{
    return true;
}

// executing regular routines ...
require 'index.php';
