<?php

namespace Tx\CzSimpleCal\Utility;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010 Christian Zenker <christian.zenker@599media.de>, 599media GmbH
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Event config utility.
 */
class EventConfig
{
    public function getRecurranceSubtype($config)
    {
        if (is_array($config['row']['recurrance_type'])) {
            $type = reset($config['row']['recurrance_type']);
        } elseif (is_string($config['row']['recurrance_type'])) {
            $type = $config['row']['recurrance_type'];
        } else {
            return;
        }

        $type = trim($type);

        if (empty($type)) {
            return;
        }

        $className = 'Tx\\CzSimpleCal\\Recurrance\\Type\\' . GeneralUtility::underscoredToUpperCamelCase($type);

        if (!class_exists($className)) {
            return;
        }

        $recurranceType = GeneralUtility::makeInstance($className);
        if (!method_exists($recurranceType, 'getSubtypes')) {
            return;
        }

        $config['items'] = $recurranceType->getSubtypes();
    }
}
