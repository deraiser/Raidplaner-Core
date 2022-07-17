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
define(["require", "exports", "tslib", "WoltLabSuite/Core/Ajax", "WoltLabSuite/Core/Event/Handler", "WoltLabSuite/Core/Ui/Notification"], function (require, exports, tslib_1, Ajax, EventHandler, UiNotification) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.init = void 0;
    Ajax = tslib_1.__importStar(Ajax);
    EventHandler = tslib_1.__importStar(EventHandler);
    UiNotification = tslib_1.__importStar(UiNotification);
    class UiEventMarkAllAsRead {
        constructor() {
            document.querySelectorAll(".markAllAsReadButton").forEach((button) => {
                button.addEventListener("click", this.click.bind(this));
            });
        }
        click(event) {
            event.preventDefault();
            Ajax.api(this);
        }
        _ajaxSuccess() {
            /* remove obsolete badges */
            // main menu
            document.querySelectorAll(".mainMenu .active .badge").forEach((badge) => badge.remove());
            // mobile page menu badge
            document.querySelectorAll(".pageMainMenuMobile .active").forEach((container) => {
                var _a, _b;
                (_b = (_a = container.closest(".menuOverlayItem")) === null || _a === void 0 ? void 0 : _a.querySelector(".badge")) === null || _b === void 0 ? void 0 : _b.remove();
            });
            // event list
            document.querySelectorAll(".rpEvent.isNew").forEach((el) => el.classList.remove("isNew"));
            EventHandler.fire("com.woltlab.wcf.MainMenuMobile", "updateButtonState");
            UiNotification.show();
        }
        _ajaxSetup() {
            return {
                data: {
                    actionName: "markAllAsRead",
                    className: "rp\\data\\event\\EventAction",
                },
            };
        }
    }
    function init() {
        new UiEventMarkAllAsRead();
    }
    exports.init = init;
});
