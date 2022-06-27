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
 * Provides suggestions for characters.
 *
 * @author      Marco Daries
 * @module      Daries/RP/Ui/Character/Search/Input
 * @see         module:WoltLabSuite/Core/Ui/Search/Input
 */

import * as Core from "WoltLabSuite/Core/Core";
import { SearchInputOptions } from "WoltLabSuite/Core/Ui/Search/Data";
import UiSearchInput from "WoltLabSuite/Core/Ui/Search/Input";

class UiCharacterSearchInput extends UiSearchInput {
    constructor(element: HTMLInputElement, options: SearchInputOptions) {
        options = Core.extend(
        {
            ajax: {
                className: "rp\\data\\character\\CharacterAction",
            },
        },
            options,
        );

        super(element, options);
    }
    
    protected createListItem(item: CharacterListItemData): HTMLLIElement {
        const listItem = super.createListItem(item);

        const box = document.createElement("div");
        box.className = "box16";
        box.innerHTML = item.icon;
        box.appendChild(listItem.children[0]);
        listItem.appendChild(box);

        return listItem;
  }
}

Core.enableLegacyInheritance(UiCharacterSearchInput);

export = UiCharacterSearchInput;

// https://stackoverflow.com/a/50677584/782822
// This is a dirty hack, because the ListItemData cannot be exported for compatibility reasons.
type FirstArgument<T> = T extends (arg1: infer U, ...args: any[]) => any ? U : never;

interface CharacterListItemData extends FirstArgument<UiSearchInput["createListItem"]> {
    icon: string;
}