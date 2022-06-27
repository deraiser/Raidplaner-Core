<?php

namespace rp\system\form\builder\field\character\avatar;

use wcf\data\IStorableObject;
use wcf\system\file\upload\UploadFile;
use wcf\system\form\builder\field\UploadFormField;
use wcf\util\ImageUtil;

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
 * Implementation of a form field for to upload character avatars.
 *
 * @author      Marco Daries
 * @package     Daries\RP\System\Form\Builder\Field\Character\Avatar
 */
class CharacterAvatarUploadFormField extends UploadFormField
{

    /**
     * @inheritDoc
     *
     * @throws \InvalidArgumentException    if the getter for the value provides invalid values
     */
    public function updatedObject(array $data, IStorableObject $object, $loadValues = true): CharacterAvatarUploadFormField
    {
        if ($loadValues) {
            // first check, whether an getter for the field exists
            if (\method_exists($object, 'get' . \ucfirst($this->getObjectProperty()) . 'UploadFileLocations')) {
                $value = \call_user_func([
                    $object,
                    'get' . \ucfirst($this->getObjectProperty()) . 'UploadFileLocations',
                ]);
                $method = "method '" . \get_class($object) . "::get" . \ucfirst($this->getObjectProperty()) . "UploadFileLocations()'";
            } elseif (\method_exists($object, 'get' . \ucfirst($this->getObjectProperty()))) {
                $value = \call_user_func([$object, 'get' . \ucfirst($this->getObjectProperty())]);
                $method = "method '" . \get_class($object) . "::get" . \ucfirst($this->getObjectProperty()) . "()'";
            } else {
                $value = $data[$this->getObjectProperty()];
                $method = "variable '" . \get_class($object) . "::$" . $this->getObjectProperty() . "'";
            }

            if ($value !== null) {
                if (\is_array($value)) {
                    $value = \array_map(function ($v) use ($method) {
                        if (!\is_string($v) || !\file_exists($v)) {
                            throw new \InvalidArgumentException(
                                    "The " . $method . " must return an array of strings with the file locations."
                            );
                        }

                        return new UploadFile(
                        $v,
                        \basename($v),
                        ImageUtil::isImage($v, \basename($v), $this->svgImageAllowed()),
                        true,
                        $this->svgImageAllowed()
                        );
                    }, $value);

                    $this->value($value);
                } else {
                    throw new \InvalidArgumentException(
                            "The " . $method . " must return an array of strings with the file locations."
                    );
                }
            }
        }

        return $this;
    }
}
