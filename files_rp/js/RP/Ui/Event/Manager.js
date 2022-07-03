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
define(["require", "exports", "tslib", "WoltLabSuite/Core/Core", "WoltLabSuite/Core/Event/Handler", "WoltLabSuite/Core/Ui/Message/Manager"], function (require, exports, tslib_1, Core, EventHandler, Manager_1) {
    "use strict";
    Core = tslib_1.__importStar(Core);
    EventHandler = tslib_1.__importStar(EventHandler);
    Manager_1 = tslib_1.__importDefault(Manager_1);
    class UiEventManager extends Manager_1.default {
        constructor(eventId) {
            super({
                className: "rp\\data\\event\\EventAction",
                selector: ".rpEventHeader",
            });
            this.eventId = eventId;
        }
        _ajaxSuccess(_data) {
            EventHandler.fire("Daries/RP/Ui/Event/Manager", "_ajaxSuccess", _data);
        }
    }
    Core.enableLegacyInheritance(UiEventManager);
    return UiEventManager;
});
