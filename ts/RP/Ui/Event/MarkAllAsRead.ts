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
 * Handles the 'mark as read' action for events.
 *
 * @author      Marco Daries
 * @module      Daries/RP/Ui/Event/MarkAllAsRead
 */

import * as Ajax from "WoltLabSuite/Core/Ajax";
import { AjaxCallbackObject, AjaxCallbackSetup } from "WoltLabSuite/Core/Ajax/Data";
import * as EventHandler from "WoltLabSuite/Core/Event/Handler";
import * as UiNotification from "WoltLabSuite/Core/Ui/Notification";

class UiEventMarkAllAsRead implements AjaxCallbackObject {
    constructor() {
        document.querySelectorAll(".markAllAsReadButton").forEach((button) => {
            button.addEventListener("click", this.click.bind(this));
        });
    }

    private click(event: MouseEvent): void {
        event.preventDefault();

        Ajax.api(this);
    }

    _ajaxSuccess(): void {
        /* remove obsolete badges */
        // main menu
        document.querySelectorAll(".mainMenu .active .badge").forEach((badge) => badge.remove());
        // mobile page menu badge
        document.querySelectorAll(".pageMainMenuMobile .active").forEach((container) => {
            container.closest(".menuOverlayItem")?.querySelector(".badge")?.remove();
        });
        
        // event list
        document.querySelectorAll(".rpEvent.isNew").forEach((el) => el.classList.remove("isNew"));

        EventHandler.fire("com.woltlab.wcf.MainMenuMobile", "updateButtonState");
        UiNotification.show();
    }

    _ajaxSetup(): ReturnType<AjaxCallbackSetup> {
        return {
            data: {
                actionName: "markAllAsRead",
                className: "rp\\data\\event\\EventAction",
            },
        };
    }
}

export function init(): void {
    new UiEventMarkAllAsRead();
}