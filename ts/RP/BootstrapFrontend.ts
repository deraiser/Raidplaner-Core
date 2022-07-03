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
 * Bootstraps RP's JavaScript with additions for the frontend usage.
 *
 * @author      Marco Daries
 * @module      Daries/RP/BootstrapFrontend
 */
 
import * as ControllerPopover from "WoltLabSuite/Core/Controller/Popover";
 
/**
 * Initializes character profile popover.
 */
function _initCharacterPopover(): void {
    ControllerPopover.init({
        className: "rpCharacterLink",
        dboAction: "rp\\data\\character\\CharacterProfileAction",
        identifier: "info.daries.rp.character",
    });
}

/**
 * Initializes event popover.
 */
function _initEventPopover(): void {
    ControllerPopover.init({
        className: "rpEventLink",
        dboAction: "rp\\data\\event\\EventAction",
        identifier: "info.daries.rp.event",
    });
}

/**
 * Bootstraps general modules and frontend exclusive ones.
 */
export function setup(options: BootstrapOptions): void {
    if (options.enableCharacterPopover) {
        _initCharacterPopover();
    }
    if (options.enableEventPopover) {
        _initEventPopover();
    }
}

interface BootstrapOptions {
    enableCharacterPopover: boolean;
    enableEventPopover: boolean;
}