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
 * Handles a character disable/enable button.
 *
 * @author      Marco Daries
 * @module      Daries/RP/Acp/Ui/Character/Action/DeleteAction
 */

import AbstractCharacterAction from "./Abstract";
import * as Ajax from "WoltLabSuite/Core/Ajax";
import { AjaxCallbackObject, AjaxCallbackSetup, DatabaseObjectActionResponse } from "WoltLabSuite/Core/Ajax/Data";
import * as Core from "WoltLabSuite/Core/Core";
import * as EventHandler from "WoltLabSuite/Core/Event/Handler";
import * as UiNotification from "WoltLabSuite/Core/Ui/Notification";

export class DisableAction extends AbstractCharacterAction implements AjaxCallbackObject {
    public constructor(button: HTMLElement, characterId: number, characterDataElement: HTMLElement) {
        super(button, characterId, characterDataElement);
        
        this.button.addEventListener("click", (event) => {
            event.preventDefault();
            const isEnabled = Core.stringToBool(this.characterDataElement.dataset.enabled!);

            Ajax.api(this, {
                actionName: isEnabled ? "disable" : "enable",
            });
        });
    }
    
    _ajaxSetup(): ReturnType<AjaxCallbackSetup> {
        return {
            data: {
                className: "rp\\data\\character\\CharacterAction",
                objectIDs: [this.characterId],
            },
        };
    }
    
    _ajaxSuccess(data: DatabaseObjectActionResponse): void {
        data.objectIDs.forEach((objectId) => {
            if (~~objectId == this.characterId) {
                switch (data.actionName) {
                    case "enable":
                        this.characterDataElement.dataset.enabled = "true";
                        this.button.textContent = this.button.dataset.disableMessage!;
                        break;

                    case "disable":
                        this.characterDataElement.dataset.enabled = "false";
                        this.button.textContent = this.button.dataset.enableMessage!;
                        break;

                    default:
                        throw new Error("Unreachable");
                }
            }
        });

        UiNotification.show();

        EventHandler.fire("info.daries.rp.acp.character", "refresh", {
            characterIds: [this.characterId],
        });
  }
}

export default DisableAction;