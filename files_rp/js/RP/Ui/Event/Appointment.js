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
define(["require", "exports", "tslib", "WoltLabSuite/Core/Core", "WoltLabSuite/Core/Ajax", "WoltLabSuite/Core/Dom/Util", "WoltLabSuite/Core/Event/Handler", "WoltLabSuite/Core/Ui/Notification"], function (require, exports, tslib_1, Core, Ajax, Util_1, EventHandler, UiNotification) {
    "use strict";
    Core = tslib_1.__importStar(Core);
    Ajax = tslib_1.__importStar(Ajax);
    Util_1 = tslib_1.__importDefault(Util_1);
    EventHandler = tslib_1.__importStar(EventHandler);
    UiNotification = tslib_1.__importStar(UiNotification);
    const appointments = new Map();
    class EventAppointment {
        constructor(eventId, userId) {
            this.eventId = eventId;
            this.userId = userId;
            this.acceptedButton = document.querySelector(".jsButtonEventAccepted");
            this.acceptedButton.addEventListener("click", (ev) => this.click(ev));
            this.canceledButton = document.querySelector(".jsButtonEventCanceled");
            this.canceledButton.addEventListener("click", (ev) => this.click(ev));
            this.maybeButton = document.querySelector(".jsButtonEventMaybe");
            this.maybeButton.addEventListener("click", (ev) => this.click(ev));
            document.querySelectorAll(".jsEventAccepted .containerList > LI").forEach((appointment) => this.initAppointment(appointment, "accepted"));
            document.querySelectorAll(".jsEventCanceled .containerList > LI").forEach((appointment) => this.initAppointment(appointment, "canceled"));
            document.querySelectorAll(".jsEventMaybe .containerList > LI").forEach((appointment) => this.initAppointment(appointment, "maybe"));
            const header = document.querySelector(".rpEventHeader");
            const disable = (header.dataset.isDeleted === "1" || header.dataset.isDisabled === "1") ? true : false;
            if (disable) {
                this.disableButton(this.acceptedButton);
                this.disableButton(this.canceledButton);
                this.disableButton(this.maybeButton);
            }
            EventHandler.add("Daries/RP/Ui/Event/Manager", "_ajaxSuccess", (data) => this.managerSuccess(data));
        }
        click(event) {
            const button = event.currentTarget;
            const status = button.dataset.status;
            if (button.disabled)
                return;
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
        disableButton(button) {
            button.classList.add("disabled");
            button.disabled = true;
        }
        enableButton(button) {
            button.classList.remove("disabled");
            button.disabled = false;
        }
        initAppointment(appointment, status) {
            const userId = ~~appointment.dataset.objectId;
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
        managerSuccess(data) {
            var _a, _b, _c;
            let hasEvent = false;
            Array.from(data.objectIDs).forEach((objectId) => {
                if (objectId === this.eventId)
                    hasEvent = true;
            });
            if (hasEvent) {
                switch (data.actionName) {
                    case "disable":
                    case "trash":
                        this.disableButton(this.acceptedButton);
                        this.disableButton(this.canceledButton);
                        this.disableButton(this.maybeButton);
                        break;
                    case "enable":
                    case "restore":
                        if (((_a = appointments.get(this.userId)) === null || _a === void 0 ? void 0 : _a.status) !== "accepted") {
                            this.enableButton(this.acceptedButton);
                        }
                        if (((_b = appointments.get(this.userId)) === null || _b === void 0 ? void 0 : _b.status) !== "canceled") {
                            this.enableButton(this.canceledButton);
                        }
                        if (((_c = appointments.get(this.userId)) === null || _c === void 0 ? void 0 : _c.status) !== "maybe") {
                            this.enableButton(this.maybeButton);
                        }
                        break;
                }
            }
        }
        _ajaxSetup() {
            return {
                data: {
                    actionName: "changeEventAppointmentStatus",
                    className: "rp\\data\\event\\EventAction"
                }
            };
        }
        _ajaxSuccess(data) {
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
                        document.querySelectorAll(".jsEventAccepted .containerList > LI").forEach((appointment) => {
                            const userId = ~~appointment.dataset.objectId;
                            if (data.returnValues.userID === userId) {
                                appointment.remove();
                            }
                        });
                        break;
                    case "canceled":
                        document.querySelectorAll(".jsEventCanceled .containerList > LI").forEach((appointment) => {
                            const userId = ~~appointment.dataset.objectId;
                            if (data.returnValues.userID === userId) {
                                appointment.remove();
                            }
                        });
                        break;
                    case "maybe":
                        document.querySelectorAll(".jsEventMaybe .containerList > LI").forEach((appointment) => {
                            const userId = ~~appointment.dataset.objectId;
                            if (data.returnValues.userID === userId) {
                                appointment.remove();
                            }
                        });
                        break;
                }
            }
            let object;
            switch (data.returnValues.status) {
                case "accepted":
                    this.disableButton(this.acceptedButton);
                    object = document.querySelector(".jsEventAccepted .containerList");
                    if (object === undefined) {
                        document.querySelector(".jsEventAccepted .info").remove();
                        document.querySelector(".jsEventAccepted").appendChild(this._newObject());
                    }
                    Util_1.default.insertHtml(data.returnValues.template, document.querySelector(".jsEventAccepted .containerList"), "append");
                    break;
                case "canceled":
                    this.disableButton(this.canceledButton);
                    object = document.querySelector(".jsEventCanceled .containerList");
                    if (object === undefined) {
                        document.querySelector(".jsEventCanceled .info").remove();
                        document.querySelector(".jsEventCanceled").appendChild(this._newObject());
                    }
                    Util_1.default.insertHtml(data.returnValues.template, document.querySelector(".jsEventCanceled .containerList"), "append");
                    break;
                case "maybe":
                    this.disableButton(this.maybeButton);
                    object = document.querySelector(".jsEventMaybe .containerList");
                    if (object === undefined) {
                        document.querySelector(".jsEventMaybe .info").remove();
                        document.querySelector(".jsEventMaybe").appendChild(this._newObject());
                    }
                    Util_1.default.insertHtml(data.returnValues.template, document.querySelector(".jsEventMaybe .containerList"), "append");
                    break;
            }
            appointments.set(data.returnValues.userID, {
                status: data.returnValues.status
            });
            UiNotification.show();
        }
        _newObject() {
            const newObject = document.createElement("ol");
            newObject.className = "containerList tripleColumned";
            return newObject;
        }
    }
    Core.enableLegacyInheritance(EventAppointment);
    return EventAppointment;
});
