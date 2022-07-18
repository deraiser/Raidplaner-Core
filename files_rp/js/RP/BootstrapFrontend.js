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
define(["require", "exports", "tslib", "WoltLabSuite/Core/Controller/Popover"], function (require, exports, tslib_1, ControllerPopover) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.setup = void 0;
    ControllerPopover = tslib_1.__importStar(ControllerPopover);
    /**
     * Initializes character profile popover.
     */
    function _initCharacterPopover() {
        ControllerPopover.init({
            className: "rpCharacterLink",
            dboAction: "rp\\data\\character\\CharacterProfileAction",
            identifier: "info.daries.rp.character",
        });
    }
    /**
     * Initializes event popover.
     */
    function _initEventPopover() {
        ControllerPopover.init({
            className: "rpEventLink",
            dboAction: "rp\\data\\event\\EventAction",
            identifier: "info.daries.rp.event",
        });
    }
    /**
     * Initializes event raid attendee popover.
     */
    function _initEventRaidAttendeePopover() {
        ControllerPopover.init({
            className: "rpEventRaidAttendeeLink",
            dboAction: "rp\\data\\event\\raid\\attendee\\EventRaidAttendeeAction",
            identifier: "info.daries.rp.event.raid.attendee",
        });
    }
    /**
     * Initializes item popover.
     */
    function _initItemPopover() {
        ControllerPopover.init({
            className: "rpItemLink",
            dboAction: "rp\\data\\item\\ItemAction",
            identifier: "info.daries.rp.item",
        });
    }
    /**
     * Bootstraps general modules and frontend exclusive ones.
     */
    function setup(options) {
        if (options.enableCharacterPopover) {
            _initCharacterPopover();
            _initEventRaidAttendeePopover();
        }
        if (options.enableEventPopover) {
            _initEventPopover();
        }
        if (options.enableItemPopover) {
            _initItemPopover();
        }
    }
    exports.setup = setup;
});
