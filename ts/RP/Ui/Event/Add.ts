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
 * Provides the dialog overlay to add a new event.
 * 
 * @author      Marco Daries
 * @module      Daries/RP/Ui/Event/Add
 */

import * as Language from "WoltLabSuite/Core/Language";
import { DialogCallbackObject, DialogCallbackSetup } from "WoltLabSuite/Core/Ui/Dialog/Data";
import UiDialog from "WoltLabSuite/Core/Ui/Dialog";

class EventAdd implements DialogCallbackObject {
    constructor(private readonly link: string) {
        document.querySelectorAll(".jsButtonEventAdd").forEach((button: HTMLElement) => {
            button.addEventListener("click", (ev) => this.openDialog(ev));
        });
    }

    openDialog(event?: MouseEvent): void {
        if (event instanceof Event) {
            event.preventDefault();
        }

        UiDialog.open(this);
    }

    _dialogSetup(): ReturnType<DialogCallbackSetup> {
        return {
            id: "eventAddDialog",
            options: {
                onSetup: (content) => {
                    const button = content.querySelector("button") as HTMLElement;
                    button.addEventListener("click", (event) => {
                        event.preventDefault();

                        const input = content.querySelector('input[name="objectTypeID"]:checked') as HTMLInputElement;

                        window.location.href = this.link.replace("{$objectTypeID}", input.value);
                    });
                },
                title: Language.get("rp.event.add")
            },
        };
    }
}

let eventAdd: EventAdd;

/**
 * Initializes the event add handler.
 */
export function init(link: string): void {
  if (!eventAdd) {
    eventAdd = new EventAdd(link);
  }
}

/**
 * Opens the 'Add Event' dialog.
 */
export function openDialog(): void {
  eventAdd.openDialog();
}