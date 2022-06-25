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
 * Handles a character delete button.
 *
 * @author      Marco Daries
 * @module      Daries/RP/Acp/Ui/Character/Action/DeleteAction
 */
 
import AbstractCharacterAction from "./Abstract";
import Delete from "./Handler/Delete";

export class DeleteAction extends AbstractCharacterAction {
    public constructor(button: HTMLElement, characterId: number, characterDataElement: HTMLElement) {
        super(button, characterId, characterDataElement);
        
        if (typeof this.button.dataset.confirmMessage !== "string") {
            throw new Error("The button does not provide a confirmMessage.");
        }
        
        this.button.addEventListener("click", (event) => {
            event.preventDefault();

            const deleteHandler = new Delete(
                [this.characterId],
                () => {
                    this.characterDataElement.remove();
                },
                this.button.dataset.confirmMessage!,
            );
            deleteHandler.delete();
        });
    }
}

export default DeleteAction;