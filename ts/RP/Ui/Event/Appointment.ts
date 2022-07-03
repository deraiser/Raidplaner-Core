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
 *  Provides participation in events.
 *
 * @author      Marco Daries
 * @module      Daries/RP/Ui/Event/Appointment
 */

import * as Core from "WoltLabSuite/Core/Core";
import * as Ajax from "WoltLabSuite/Core/Ajax";
import { AjaxCallbackSetup, DatabaseObjectActionResponse } from "WoltLabSuite/Core/Ajax/Data"; 
import DomUtil from "WoltLabSuite/Core/Dom/Util";
import * as EventHandler from "WoltLabSuite/Core/Event/Handler";
import * as UiNotification from "WoltLabSuite/Core/Ui/Notification";

const appointments = new Map<number, AppointmentData>();

class EventAppointment {
    private acceptedButton: HTMLButtonElement;
    private canceledButton: HTMLButtonElement;
    private readonly eventId: number;
    private maybeButton: HTMLButtonElement;
    private readonly userId: number;
    
    constructor(eventId: number, userId: number) {
        this.eventId = eventId;
        this.userId = userId;
        
        this.acceptedButton = document.querySelector(".jsButtonEventAccepted") as HTMLButtonElement;
        this.acceptedButton.addEventListener("click", (ev) => this.click(ev));

        this.canceledButton = document.querySelector(".jsButtonEventCanceled") as HTMLButtonElement;
        this.canceledButton.addEventListener("click", (ev) => this.click(ev));

        this.maybeButton = document.querySelector(".jsButtonEventMaybe") as HTMLButtonElement;
        this.maybeButton.addEventListener("click", (ev) => this.click(ev));

        document.querySelectorAll(".jsEventAccepted .containerList > LI").forEach((appointment: HTMLLIElement) => this.initAppointment(appointment, "accepted"));
        document.querySelectorAll(".jsEventCanceled .containerList > LI").forEach((appointment: HTMLLIElement) => this.initAppointment(appointment, "canceled"));
        document.querySelectorAll(".jsEventMaybe .containerList > LI").forEach((appointment: HTMLLIElement) => this.initAppointment(appointment, "maybe"));
        
        const header = document.querySelector(".rpEventHeader") as HTMLElement;
        const disable = (header.dataset.isDeleted === "1" || header.dataset.isDisabled === "1") ? true : false;
        if (disable) {
            this.disableButton(this.acceptedButton);
            this.disableButton(this.canceledButton);
            this.disableButton(this.maybeButton);
        }
        
        EventHandler.add("Daries/RP/Ui/Event/Manager", "_ajaxSuccess", (data: DatabaseObjectActionResponse) => this.managerSuccess(data));
    }
    
    private click(event: MouseEvent): void {
        const button = event.currentTarget as HTMLButtonElement;
        const status = button.dataset.status;

        if (button.disabled) return;

        const appointment = appointments.get(this.userId);
        if (appointment && status === appointment.status) {
            return;
        }

        Ajax.api(this, {
            parameters: {
                eventID: this.eventId,
                status: status,
                userID: this.userId,
                exists: appointment ? 1 : 0
            }
        });
    }
    
    private disableButton(button: HTMLButtonElement) {
        button.classList.add("disabled");
        button.disabled = true;
    }

    private enableButton(button: HTMLButtonElement) {
        button.classList.remove("disabled");
        button.disabled = false;
    }

    private initAppointment(appointment: HTMLElement, status: string) {
        const userId = ~~appointment.dataset.objectId!;
            
        if (this.userId === userId) {
            switch (status) {
                case "accepted":
                    this.disableButton(this.acceptedButton);
                    break;
                case "canceled":
                    this.disableButton(this.canceledButton);
                    break;
                case "maybe":
                    this.disableButton(this.maybeButton);
                    break;
            }
        }

        appointments.set(userId, {
            status: status
        });
    }
    
    protected managerSuccess(data: DatabaseObjectActionResponse): void {
        let hasEvent = false;
        Array.from(data.objectIDs).forEach((objectId: number) => {
            if (objectId === this.eventId) hasEvent = true;
        });
        
        if (hasEvent) {
            switch(data.actionName) {
                case "disable":
                case "trash":
                        this.disableButton(this.acceptedButton);
                        this.disableButton(this.canceledButton);
                        this.disableButton(this.maybeButton);
                        break;
                case "enable":
                case "restore":
                    const appointment = appointments.get(this.userId) as AppointmentData;
                    if (appointment?.status !== "accepted") {
                        this.enableButton(this.acceptedButton);
                    }
                    if (appointment?.status !== "canceled") {
                        this.enableButton(this.canceledButton);
                    }
                    if (appointment?.status !== "maybe") {
                        this.enableButton(this.maybeButton);
                    }
                    break;
            }
        }
    }

    _ajaxSetup(): ReturnType<AjaxCallbackSetup> {
        return {
            data: {
                actionName: "changeEventAppointmentStatus",
                className: "rp\\data\\event\\EventAction"
            }
        };
    }

    _ajaxSuccess(data: AjaxResponse): void {
        // remove old appointment by user id
        const appointment = appointments.get(data.returnValues.userID);
        if (appointment) {
            switch (appointment.status) {
                case "accepted":
                    this.enableButton(this.acceptedButton);
                    break;
                case "canceled":
                    this.enableButton(this.canceledButton);
                    break;
                case "maybe":
                    this.enableButton(this.maybeButton);
                    break;
            }
            
            switch (appointment.status) {
                case "accepted":
                    document.querySelectorAll(".jsEventAccepted .containerList > LI").forEach((appointment: HTMLLIElement) => {
                        const userId = ~~appointment!.dataset.objectId;
                        if (data.returnValues.userID === userId) {
                            appointment.remove();
                        }
                    });
                    break;
                case "canceled":
                    document.querySelectorAll(".jsEventCanceled .containerList > LI").forEach((appointment: HTMLLIElement) => {
                        const userId = ~~appointment!.dataset.objectId;
                        if (data.returnValues.userID === userId) {
                            appointment.remove();
                        }
                    });
                    break;
                case "maybe":
                    document.querySelectorAll(".jsEventMaybe .containerList > LI").forEach((appointment: HTMLLIElement) => {
                        const userId = ~~appointment!.dataset.objectId;
                        if (data.returnValues.userID === userId) {
                            appointment.remove();
                        }
                    });
                    break;
            }
        }

        switch (data.returnValues.status) {
            case "accepted":
                this.disableButton(this.acceptedButton);

                const object = document.querySelector(".jsEventAccepted .containerList") as HTMLOListElement;
                if (!object) {
                    document.querySelector(".jsEventAccepted .info")!.remove();
                    document.querySelector(".jsEventAccepted")!.appendChild(this._newObject());
                }
                DomUtil.insertHtml(data.returnValues.template, document.querySelector(".jsEventAccepted .containerList")!, "append");
                break;
            case "canceled":
                this.disableButton(this.canceledButton);

                const object1 = document.querySelector(".jsEventCanceled .containerList") as HTMLOListElement;
                if (!object1) {
                    document.querySelector(".jsEventCanceled .info")!.remove();
                    document.querySelector(".jsEventCanceled")!.appendChild(this._newObject());
                }
                DomUtil.insertHtml(data.returnValues.template, document.querySelector(".jsEventCanceled .containerList")!, "append");
                break;
            case "maybe":
                this.disableButton(this.maybeButton);

                const object2 = document.querySelector(".jsEventMaybe .containerList") as HTMLOListElement;
                if (!object2) {
                    document.querySelector(".jsEventMaybe .info")!.remove();
                    document.querySelector(".jsEventMaybe")!.appendChild(this._newObject());
                }
                DomUtil.insertHtml(data.returnValues.template, document.querySelector(".jsEventMaybe .containerList")!, "append");
                break;
        }

        appointments.set(data.returnValues.userID, {
            status: data.returnValues.status
        });

        UiNotification.show();
    }

    _newObject(): HTMLOListElement {
        const newObject = document.createElement("ol");
        newObject.className = "containerList tripleColumned";
        return newObject;
    }
}

Core.enableLegacyInheritance(EventAppointment);

export = EventAppointment;

interface AppointmentData {
    status: string;
}

interface AjaxResponse extends DatabaseObjectActionResponse {
    returnValues: {
        status: string;
        template: string;
        userID: number;
    };
}