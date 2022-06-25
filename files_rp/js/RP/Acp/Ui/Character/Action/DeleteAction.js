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
define(["require", "exports", "tslib", "./Abstract", "./Handler/Delete"], function (require, exports, tslib_1, Abstract_1, Delete_1) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.DeleteAction = void 0;
    Abstract_1 = tslib_1.__importDefault(Abstract_1);
    Delete_1 = tslib_1.__importDefault(Delete_1);
    class DeleteAction extends Abstract_1.default {
        constructor(button, characterId, characterDataElement) {
            super(button, characterId, characterDataElement);
            if (typeof this.button.dataset.confirmMessage !== "string") {
                throw new Error("The button does not provide a confirmMessage.");
            }
            this.button.addEventListener("click", (event) => {
                event.preventDefault();
                const deleteHandler = new Delete_1.default([this.characterId], () => {
                    this.characterDataElement.remove();
                }, this.button.dataset.confirmMessage);
                deleteHandler.delete();
            });
        }
    }
    exports.DeleteAction = DeleteAction;
    exports.default = DeleteAction;
});
