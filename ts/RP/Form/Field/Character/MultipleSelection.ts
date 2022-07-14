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
 * Handle other characters these Users by selection.
 *
 * @author      Marco Daries
 * @module      Daries/RP/Form/Field/Character/MultipleSelection
 */

import * as Core from "WoltLabSuite/Core/Core";

class CharacterMultipleSelection {
    protected readonly _element: HTMLElement;
    
    constructor(elementId: string) {
        this._element = document.getElementById(elementId)as HTMLElement;
        this._element.querySelectorAll("input").forEach((input) => {
           input.addEventListener("change", (ev) => this._change(ev)); 
        });
    }
    
    protected _change(event: Event): void {
        const element = event.currentTarget as HTMLInputElement;
        const userId = ~~element.dataset.userId!;
        const value = element.value;
        const checked = element.checked;
        
        this._element.querySelectorAll("input").forEach((input) => {
            if (userId === ~~input.dataset.userId! &&
                value !== input.value) {
                if (checked) input.disabled = true;
                else input.disabled = false; 
            }
        });
    }
}

Core.enableLegacyInheritance(CharacterMultipleSelection);

export = CharacterMultipleSelection;