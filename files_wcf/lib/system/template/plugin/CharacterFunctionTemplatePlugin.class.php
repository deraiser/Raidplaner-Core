<?php

namespace wcf\system\template\plugin;

use rp\data\character\CharacterProfile;
use wcf\system\template\TemplateEngine;
use wcf\util\ClassUtil;
use wcf\util\StringUtil;

/*  Project:    Raidplaner: Core
 *  Package:    info.daries.rp
 *  Link:       http://daries.info
 *
 *  Copyright (C) 2018-2022 Daries.info Developer Team
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published
 *  by the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Template function plugin which generates links to characters.
 * 
 * Attributes:
 * - `object` (required) has to be a (decorated) `Character` object.
 * - `type` (optional) supports the following values:
 *      - `default` (default value) generates a link with the character title with popover support.
 *      - `formated` generates a link with the formatted character title with popover support.
 *      - `avatarXY` generates a link with the character's avatar in size `XY`.
 *      - `plain` generates a link link without character title formatting and popover support
 * - `append` (optional) is appended to the character link.
 * 
 * All other additional parameter values are added as attributes to the `a` element. Parameter names
 * in camel case are changed to kebab case (`fooBar` becomes `foo-bar`).
 *
 * Usage:
 *      {character object=$character}
 *      {character object=$character type='plain'}
 *      {character object=$character type='avatar48'}
 * 
 * @author      Marco Daries
 * @package     WoltLabSuite\Core\System\Template\Plugin
 */
class CharacterFunctionTemplatePlugin implements IFunctionTemplatePlugin
{

    /**
     * @inheritDoc
     */
    public function execute($tagArgs, TemplateEngine $tplObj): string
    {
        if (!isset($tagArgs['object'])) {
            throw new \InvalidArgumentException("Missing 'object' attribute.");
        }

        $object = $tagArgs['object'];
        unset($tagArgs['object']);
        if (!($object instanceof CharacterProfile) && !ClassUtil::isDecoratedInstanceOf($object, CharacterProfile::class)) {
            $type = \gettype($object);
            if (\is_object($object)) {
                $type = "'" . \get_class($object) . "' object";
            }

            throw new \InvalidArgumentException(
                    "'object' attribute is no '" . CharacterProfile::class . "' object, instead {$type} given."
            );
        }

        $additionalParameters = '';
        $content = '';
        if (isset($tagArgs['type'])) {
            $type = $tagArgs['type'];
            unset($tagArgs['type']);

            if ($type === 'plain') {
                $content = StringUtil::encodeHTML($object->getTitle());
            } else if (\preg_match('~^avatar(\d+)$~', $type, $matches)) {
                $content = $object->getAvatar()->getImageTag($matches[1]);
            } else if ($type !== 'default') {
                throw new \InvalidArgumentException("Unknown 'type' value '{$type}'.");
            }
        }

        // default case
        if ($content === '') {
            $content = $object->getFormatedTitle();

            if ($object->getObjectID()) {
                $additionalParameters = ' data-object-id="' . $object->getObjectID() . '"';
                if (isset($tagArgs['class'])) {
                    $tagArgs['class'] = 'rpCharacterLink ' . $tagArgs['class'];
                } else {
                    $tagArgs['class'] = 'rpCharacterLink';
                }
            }
        }

        if (isset($tagArgs['href'])) {
            throw new \InvalidArgumentException("'href' attribute is not allowed.");
        }

        $append = '';
        if (isset($tagArgs['append'])) {
            $append = $tagArgs['append'];
            unset($tagArgs['append']);
        }

        foreach ($tagArgs as $name => $value) {
            if (!\preg_match('~^[a-z]+([A-z]+)+$~', $name)) {
                throw new \InvalidArgumentException("Invalid additional argument name '{$name}'.");
            }

            $additionalParameters .= ' ' . \strtolower(\preg_replace('~([A-Z])~', '-$1', $name))
                . '="' . StringUtil::encodeHTML($value) . '"';
        }

        if (!$object->getObjectID()) {
            return '<span' . $additionalParameters . '>' . $content . '</span>';
        }

        return '<a href="' . StringUtil::encodeHTML($object->getLink() . $append) . '"' . $additionalParameters . '>' . $content . '</a>';
    }
}
