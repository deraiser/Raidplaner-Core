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
define(["require", "exports", "tslib", "WoltLabSuite/Core/Core"], function (require, exports, tslib_1, Core) {
    "use strict";
    Core = tslib_1.__importStar(Core);
    class CharacterMultipleSelection {
        constructor(elementId) {
            this._element = document.getElementById(elementId);
            this._element.querySelectorAll("input").forEach((input) => {
                input.addEventListener("change", (ev) => this._change(ev));
            });
        }
        _change(event) {
            const element = event.currentTarget;
            const userId = ~~element.dataset.userId;
            const value = element.value;
            const checked = element.checked;
            this._element.querySelectorAll("input").forEach((input) => {
                if (userId === ~~input.dataset.userId &&
                    value !== input.value) {
                    if (checked)
                        input.disabled = true;
                    else
                        input.disabled = false;
                }
            });
        }
    }
    Core.enableLegacyInheritance(CharacterMultipleSelection);
    return CharacterMultipleSelection;
});
