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
 * Deletes a given character.
 *
 * @author      Marco Daries
 * @module      Daries/RP/Acp/Ui/Character/Action/Handler/Delete
 */

import * as Ajax from "WoltLabSuite/Core/Ajax";
import { CallbackSuccess } from "WoltLabSuite/Core/Ajax/Data";
import * as UiConfirmation from "WoltLabSuite/Core/Ui/Confirmation";

export class Delete {
    private characterIDs: number[];
    private successCallback: CallbackSuccess;
    private deleteMessage: string;

    public constructor(characterIDs: number[], successCallback: CallbackSuccess, deleteMessage: string) {
        this.characterIDs = characterIDs;
        this.successCallback = successCallback;
        this.deleteMessage = deleteMessage;
    }

    public delete(): void {
        UiConfirmation.show({
            confirm: () => {
                Ajax.apiOnce({
                    data: {
                        actionName: "delete",
                        className: "rp\\data\\character\\CharacterAction",
                        objectIDs: this.characterIDs,
                    },
                    success: this.successCallback,
                });
            },
            message: this.deleteMessage,
            messageIsHtml: true,
        });
    }
}

export default Delete;