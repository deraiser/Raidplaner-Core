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
define(["require", "exports", "tslib", "WoltLabSuite/Core/Ajax", "WoltLabSuite/Core/Controller/Clipboard", "WoltLabSuite/Core/Core", "../DragAndDrop/Item", "WoltLabSuite/Core/Dom/Change/Listener", "WoltLabSuite/Core/Event/Handler", "WoltLabSuite/Core/Language", "WoltLabSuite/Core/Ui/Confirmation", "WoltLabSuite/Core/Ui/Dialog", "WoltLabSuite/Core/Ui/Dropdown/Simple", "WoltLabSuite/Core/Ui/Notification"], function (require, exports, tslib_1, Ajax, ControllerClipboard, Core, Item_1, DomChangeListener, EventHandler, Language, UiConfirmation, UiDialog, UiDropdownSimple, UiNotification) {
    "use strict";
    Ajax = tslib_1.__importStar(Ajax);
    ControllerClipboard = tslib_1.__importStar(ControllerClipboard);
    Core = tslib_1.__importStar(Core);
    Item_1 = tslib_1.__importDefault(Item_1);
    DomChangeListener = tslib_1.__importStar(DomChangeListener);
    EventHandler = tslib_1.__importStar(EventHandler);
    Language = tslib_1.__importStar(Language);
    UiConfirmation = tslib_1.__importStar(UiConfirmation);
    UiDialog = tslib_1.__importStar(UiDialog);
    UiDropdownSimple = tslib_1.__importStar(UiDropdownSimple);
    UiNotification = tslib_1.__importStar(UiNotification);
    const attendees = new Map();
    class AttendeeInlineEditor {
        /**
         * Initializes the event raid inline editor for attendees.
         */
        constructor(permissions) {
            this.permissions = Core.extend({
                canEdit: false,
            }, permissions);
            EventHandler.add("com.woltlab.wcf.clipboard", "info.daries.rp.raid.attendee", (data) => this.clipboardAction(data));
            EventHandler.add("info.daries.rp.raid.attendee", "initAttendee", (attendeeId) => this.initAttendee(undefined, ~~attendeeId));
            DomChangeListener.add("Daries/RP/Ui/Event/Raid/Attendee/InlineEditor", () => this.reloadAttendees());
        }
        /**
         * Reacts to executed clipboard actions.
         */
        clipboardAction(actionData) {
            // only consider events if the action has been executed
            if (actionData.responseData !== null) {
                const callbackFunction = new Map([
                    ["info.daries.rp.raid.attendee.delete", (eventId) => this.triggerDelete(eventId)],
                ]);
                const triggerFunction = callbackFunction.get(actionData.data.actionName);
                if (triggerFunction) {
                    actionData.responseData.objectIDs.forEach((objectId) => triggerFunction(objectId));
                    UiNotification.show();
                }
            }
            else if (actionData.data.actionName === "info.daries.rp.raid.attendee.updateStatus") {
                const dialog = UiDialog.openStatic("attendeeUpdateStatusDialog", actionData.data.internalData.template, {
                    title: Language.get("rp.event.raid.updateStatus"),
                });
                const submitButton = dialog.content.querySelector("[data-type=submit]");
                submitButton.addEventListener("click", (ev) => this.submitUpdateStatus(ev, dialog.content, actionData.data.internalData.objectIDs));
            }
        }
        /**
         * Initializes an attendee element.
         */
        initAttendee(attendee, objectId) {
            if (!attendee && ~~objectId > 0) {
                attendee = document.getElementById("attendee" + objectId);
            }
            if (!attendee)
                return;
            objectId = ~~attendee.dataset.objectId;
            if (attendees.has(objectId))
                return;
            const dropdownId = `attendreeDropdown${objectId}`;
            const dropdownMenu = UiDropdownSimple.getDropdownMenu(dropdownId);
            let buttonDelete, buttonUpdateStatus;
            if (dropdownMenu !== undefined) {
                buttonDelete = dropdownMenu.querySelector(".jsAttendeeRemove");
                buttonDelete === null || buttonDelete === void 0 ? void 0 : buttonDelete.addEventListener("click", (ev) => this.prompt(ev, objectId, "delete"));
                buttonUpdateStatus = dropdownMenu.querySelector(".jsAttendeeUpdateStatus");
                buttonUpdateStatus === null || buttonUpdateStatus === void 0 ? void 0 : buttonUpdateStatus.addEventListener("click", (ev) => this.prompt(ev, objectId, "updateStatus"));
            }
            attendees.set(objectId, {
                buttons: {
                    delete: buttonDelete,
                    updateStatus: buttonUpdateStatus,
                },
                element: attendee,
            });
        }
        invoke(objectId, actionName, parameters = {}) {
            Ajax.api(this, {
                actionName: actionName,
                objectIDs: [objectId],
                parameters: parameters
            });
        }
        /**
         * Prompts a user to confirm the clicked action before executing it.
         */
        prompt(event, objectId, actionName) {
            event.preventDefault();
            const attendee = attendees.get(objectId);
            switch (actionName) {
                case "delete":
                    UiConfirmation.show({
                        confirm: () => {
                            this.invoke(objectId, actionName);
                        },
                        message: attendee.buttons[actionName].dataset.confirmMessageHtml,
                        messageIsHtml: true,
                    });
                    break;
                case "updateStatus":
                    this.invoke(objectId, "loadUpdateStatus");
                    break;
            }
        }
        /**
         * Reads in new attendees.
         */
        reloadAttendees() {
            document.querySelectorAll(".attendee").forEach((attendee) => {
                if (attendees.has(~~attendee.dataset.objectId))
                    return;
                this.initAttendee(attendee, 0);
                if (this.permissions.canEdit) {
                    new Item_1.default(attendee);
                }
            });
        }
        /**
         * Is called, if the update status dialog form is submitted.
         */
        submitUpdateStatus(event, content, objectIds) {
            event.preventDefault();
            const select = content.querySelector("select[name=status]");
            const status = ~~select.value;
            Ajax.api(this, {
                actionName: "updateStatus",
                objectIDs: objectIds,
                parameters: {
                    status: status,
                },
            });
        }
        /**
         * Handles an attendee being deleted.
         */
        triggerDelete(attendeeId) {
            const attendee = attendees.get(attendeeId);
            if (!attendee) {
                // The affected attendee might be hidden by the filter settings.
                return;
            }
            attendee.element.remove();
            attendees.delete(attendeeId);
        }
        /**
         * Handles an attendee being update status.
         */
        triggerUpdateStatus(attendeeIds, status) {
            UiDialog.close("attendeeUpdateStatusDialog");
            attendeeIds.forEach((attendeeId) => {
                const attendee = attendees.get(attendeeId);
                if (attendee) {
                    const currentDistributionId = attendee.element.dataset.distributionId;
                    const dropdownId = `attendreeDropdown${attendeeId}`;
                    document.querySelectorAll(".attendeeBox").forEach((attendeeBox) => {
                        if (attendeeBox.dataset.objectId === currentDistributionId &&
                            ~~attendeeBox.dataset.status === status) {
                            const attendeeList = attendeeBox.querySelector(".attendeeList");
                            attendeeList.insertAdjacentElement("beforeend", attendee.element);
                        }
                    });
                }
            });
            DomChangeListener.trigger();
        }
        _ajaxSetup() {
            return {
                data: {
                    className: "rp\\data\\event\\raid\\attendee\\EventRaidAttendeeAction",
                },
            };
        }
        _ajaxSuccess(data) {
            if (data.actionName === "loadUpdateStatus") {
                const dialog = UiDialog.openStatic("attendeeUpdateStatusDialog", data.returnValues.template, {
                    title: Language.get("rp.event.raid.updateStatus"),
                });
                const submitButton = dialog.content.querySelector("[data-type=submit]");
                submitButton.addEventListener("click", (ev) => this.submitUpdateStatus(ev, dialog.content, data.objectIDs));
                return;
            }
            switch (data.actionName) {
                case "delete":
                    this.triggerDelete(data.objectIDs[0]);
                    break;
                case "updateStatus":
                    this.triggerUpdateStatus(data.objectIDs, ~~data.returnValues.status);
                    break;
            }
            UiNotification.show();
            ControllerClipboard.reload();
        }
    }
    Core.enableLegacyInheritance(AttendeeInlineEditor);
    return AttendeeInlineEditor;
});
