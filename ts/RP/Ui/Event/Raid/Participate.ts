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
 * Manages the participate button in the raid event.
 *
 * @author      Marco Daries
 * @module      Daries/RP/Ui/Event/Raid/Participate
 */

import * as Ajax from "WoltLabSuite/Core/Ajax";
import * as Core from "WoltLabSuite/Core/Core";
import * as DomChangeListener from "WoltLabSuite/Core/Dom/Change/Listener";
import * as DomUtil from "WoltLabSuite/Core/Dom/Util";
import FormBuilderDialog from "WoltLabSuite/Core/Form/Builder/Dialog";
import * as Language from "WoltLabSuite/Core/Language";
import * as UiConfirmation from "WoltLabSuite/Core/Ui/Confirmation";
import * as UiNotification from "WoltLabSuite/Core/Ui/Notification";

class EventRaidParticipate {
    protected readonly _addButton: HTMLElement;
    protected readonly _buttonContainer: HTMLElement;
    protected _dialog: FormBuilderDialog;
    protected readonly _eventId: number;
    protected readonly _options: ButtonOptions;
    protected readonly _removeButton: HTMLElement;

    /**
     * Initializes the event raid inline editor for attendees.
     */
    constructor(eventId: number, options: ButtonOptions) {
        this._options = Core.extend(
            {
                attendeeId: 0,
                canParticipate: false,
                hasAttendee: false,
            }, 
            options,
        ) as ButtonOptions;
        
        if (!this._options.canParticipate) return;
        
        this._eventId = eventId;
        
        this._buttonContainer = document.querySelector(".jsButtonAttendee") as HTMLElement;
        
        // create participate buttons
        this._addButton = this._createButton(Language.get("rp.event.raid.participate"), "fa-plus");
        this._removeButton = this._createButton(Language.get("rp.event.raid.participate.remove"), "fa-trash");
        
        this.toogleButton(this._options.hasAttendee);
        
        DomUtil.show(this._buttonContainer);
    }
    
    private _click(): void {
        if (!this._options.hasAttendee) {
            if (this._dialog === undefined) {
                this._dialog = new FormBuilderDialog("addAttendeeDialog", "rp\\data\\event\\raid\\attendee\\EventRaidAttendeeAction", "createAddDialog", {
                    dialog: {
                        title: Language.get("rp.event.raid.attendee.add"),
                    },
                    actionParameters: {
                        eventID: this._eventId,
                    },
                    submitActionName: "submitAddDialog",
                    successCallback:(data: AjaxResponse) => this._ajaxSuccess(data),
                });
            }
            
            this._dialog.open();
        } else {
            const attendee = document.getElementById(`attendee${this._options.attendeeId}`)!;
            UiConfirmation.show({
                confirm: () => {
                    Ajax.apiOnce({
                        data: {
                            actionName: "delete",
                            className: "rp\\data\\event\\raid\\attendee\\EventRaidAttendeeAction",
                            objectIDs: [ attendee.dataset.objectId! ],
                        },
                        success: () => {
                            this.toogleButton(false);
                            attendee.remove();
                            DomChangeListener.trigger();
                            UiNotification.show();
                        },
                    });
                },
                message: Language.get("rp.event.raid.attendee.remove.confirmMessage"),
                messageIsHtml: true,
            });
        }
    }
    
    private _createButton(title: string, icon: string): HTMLElement {
        const button = document.createElement("a");
        button.className = "button buttonPrimary";
        button.addEventListener("click", () => this._click());
        button.innerHTML = `
            <span class="icon icon16 ${icon}"></span>
            <span>${title}</span>
        `;
        
        return button;
    }
    
    public showButton(show: boolean): void {
        if (show) DomUtil.show(this._buttonContainer);
        else DomUtil.hide(this._buttonContainer);
    }
    
    public toogleButton(hasAttendee: boolean): void {
        if (hasAttendee) {
            this._buttonContainer.replaceChildren(this._removeButton);
            this._options.hasAttendee = true;
        } else {
            this._buttonContainer.replaceChildren(this._addButton);
            this._options.hasAttendee = false;
            this._options.attendeeId = 0;
        }
    }
    
    private _ajaxSuccess(data: AjaxResponse): void {
        document.querySelectorAll(".attendeeBox").forEach((attendeeBox: HTMLElement) => {
            if (data.distributionId === ~~attendeeBox.dataset.objectId! &&
                data.status === ~~attendeeBox.dataset.status!) {
                this._options.attendeeId = data.attendeeId;
                    
                const attendeeList = attendeeBox.querySelector(".attendeeList") as HTMLElement;
                DomUtil.insertHtml(data.template, attendeeList, "append");

                this.toogleButton(true);
                DomChangeListener.trigger();
                UiNotification.show();
            }
        });
    }
}

let _didInit = false;
let _participate: EventRaidParticipate | undefined = undefined;
export function setup(eventId: number, options: ButtonOptions): void {
    if (_didInit) return;
        _didInit = true;
        
    _participate = new EventRaidParticipate(eventId, options);
}
export function showButton(show: boolean): void {
    _participate?.showButton(show);
}

export function toogleButton(hasAttendee: boolean): void {
    _participate?.toogleButton(hasAttendee);
}

interface AjaxResponse {
    attendeeId: number;
    distributionId: number;
    status: number;
    template: string;
}

interface ButtonOptions {
    attendeeId: number;
    canParticipate: boolean;
    hasAttendee: boolean;
}