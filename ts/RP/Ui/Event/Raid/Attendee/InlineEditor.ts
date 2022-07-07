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
 * Handles attendee updateStatus and delete.
 *
 * @author      Marco Daries
 * @module      Daries/RP/Ui/Event/Raid/Attendee/InlineEditor
 */

import * as Ajax from "WoltLabSuite/Core/Ajax";
import { AjaxCallbackSetup, DatabaseObjectActionResponse } from "WoltLabSuite/Core/Ajax/Data";
import * as ControllerClipboard from "WoltLabSuite/Core/Controller/Clipboard";
import * as Core from "WoltLabSuite/Core/Core";
import DragAndDropItem from "../DragAndDrop/Item";
import * as DomChangeListener from "WoltLabSuite/Core/Dom/Change/Listener";
import * as DomUtil from "WoltLabSuite/Core/Dom/Util";
import * as EventHandler from "WoltLabSuite/Core/Event/Handler";
import * as Language from "WoltLabSuite/Core/Language";
import * as UiConfirmation from "WoltLabSuite/Core/Ui/Confirmation";
import * as UiDialog from "WoltLabSuite/Core/Ui/Dialog";
import * as UiDropdownSimple from "WoltLabSuite/Core/Ui/Dropdown/Simple";
import * as UiNotification from "WoltLabSuite/Core/Ui/Notification";
import User from "WoltLabSuite/Core/User";

const attendees = new Map<number, AttendeeData>();

class AttendeeInlineEditor {
    private readonly permissions: InlineEditorPermissions;
    /**
     * Initializes the event raid inline editor for attendees.
     */
    constructor(permissions: InlineEditorPermissions) {
        this.permissions = Core.extend(
            {
                canEdit: false,
            }, 
            permissions,
        ) as InlineEditorPermissions;
        
        
        EventHandler.add("com.woltlab.wcf.clipboard", "info.daries.rp.raid.attendee", (data) => this.clipboardAction(data));
        EventHandler.add("info.daries.rp.raid.attendee", "initAttendee", (attendeeId) => this.initAttendee(undefined, ~~attendeeId));
        
        DomChangeListener.add("Daries/RP/Ui/Event/Raid/Attendee/InlineEditor", () => this.reloadAttendees());
    }
    
    /**
     * Reacts to executed clipboard actions.
     */
    private clipboardAction(actionData: ClipboardActionData): void {
        // only consider events if the action has been executed
        if (actionData.responseData !== null) {
            const callbackFunction = new Map([
                ["info.daries.rp.raid.attendee.delete", (eventId: number) => this.triggerDelete(eventId)],
            ]);

            const triggerFunction = callbackFunction.get(actionData.data.actionName);
            if (triggerFunction) {
                actionData.responseData.objectIDs.forEach((objectId) => triggerFunction(objectId));

                UiNotification.show();
            }
        } else if (actionData.data.actionName === "info.daries.rp.raid.attendee.updateStatus") {
            const dialog = UiDialog.openStatic("attendeeUpdateStatusDialog", actionData.data.internalData.template, {
                title: Language.get("rp.event.raid.updateStatus"),
            });
            
            const submitButton = dialog.content.querySelector("[data-type=submit]") as HTMLButtonElement;
                submitButton.addEventListener("click", (ev) => this.submitUpdateStatus(ev, dialog.content, actionData.data.internalData.objectIDs));
            }
    }
    
    /**
     * Initializes an attendee element.
     */
    private initAttendee(attendee: HTMLElement | undefined, objectId: number): void {
        if (!attendee && ~~objectId > 0) {
            attendee = document.getElementById("attendee" + objectId) as HTMLElement;
        } 
        if (!attendee) return;
        objectId = ~~attendee.dataset.objectId!;
        
        if (attendees.has(objectId)) return;
        
        const dropdownId = `attendreeDropdown${objectId}`;
        const dropdownMenu = UiDropdownSimple.getDropdownMenu(dropdownId)!;

        let buttonDelete, buttonUpdateStatus;
        if (dropdownMenu !== undefined) {
            buttonDelete = dropdownMenu.querySelector(".jsAttendeeRemove") as HTMLAnchorElement;
            buttonDelete?.addEventListener("click", (ev) => this.prompt(ev, objectId, "delete"));

            buttonUpdateStatus = dropdownMenu.querySelector(".jsAttendeeUpdateStatus") as HTMLAnchorElement;
            buttonUpdateStatus?.addEventListener("click", (ev) => this.prompt(ev, objectId, "updateStatus"));
        }
        
        attendees.set(objectId, {
            buttons: {
                delete: buttonDelete,
                updateStatus: buttonUpdateStatus,
            },
            element: attendee,
        });
    }
    
    /**
     * Invokes the selected action.
     */
    private invoke(objectId: number, actionName: string): void;
    private invoke(objectId: number, actionName: string, parameters: object = {}): void {
        Ajax.api(this, {
            actionName: actionName,
            objectIDs: [objectId],
            parameters: parameters
        });
    }
    
    /**
     * Prompts a user to confirm the clicked action before executing it.
     */
    private prompt(event: MouseEvent, objectId: number, actionName: string): void {
        event.preventDefault();
        
        const attendee = attendees.get(objectId)!;
        
        switch(actionName) {
            case "delete":
                UiConfirmation.show({
                    confirm: () => {
                        this.invoke(objectId, actionName);
                    },
                    message: <string>attendee.buttons[actionName]!.dataset.confirmMessageHtml,
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
    private reloadAttendees(): void {
        document.querySelectorAll(".attendee").forEach((attendee: HTMLElement) => {
            if (attendees.has(~~attendee.dataset.objectId!)) return;
            
            this.initAttendee(attendee, 0)
            
            if (this.permissions.canEdit) {
                new DragAndDropItem(attendee);
            }
        });
    }
    
    /**
     * Is called, if the update status dialog form is submitted.
     */
    private submitUpdateStatus(event: MouseEvent, content: HTMLElement, objectIds: number[]): void {
        event.preventDefault();
        
        const select = content.querySelector("select[name=status]") as HTMLSelectElement;
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
    private triggerDelete(attendeeId: number): void {
        const attendee = attendees.get(attendeeId);
        if (!attendee) {
            // The affected attendee might be hidden by the filter settings.
            return;
        }
        
        attendee.element!.remove();
        attendees.delete(attendeeId);
    }
    
    /**
     * Handles an attendee being update status.
     */
    private triggerUpdateStatus(attendeeIds: number[], status: number): void {
        UiDialog.close("attendeeUpdateStatusDialog");
        
        attendeeIds.forEach((attendeeId) => {
            const attendee = attendees.get(attendeeId);
            
            if (attendee) {
                const currentDistributionId = attendee.element!.dataset.distributionId;
                const dropdownId = `attendreeDropdown${attendeeId}`;
                
                document.querySelectorAll(".attendeeBox").forEach((attendeeBox: HTMLElement) => {
                    if (attendeeBox.dataset.objectId === currentDistributionId &&
                        ~~attendeeBox.dataset.status! === status) {
                            
                        const attendeeList = attendeeBox.querySelector(".attendeeList") as HTMLElement;
                        attendeeList.insertAdjacentElement("beforeend", attendee.element!);
                    }
                });
            }
        });

        DomChangeListener.trigger();
    }
    
    _ajaxSetup(): ReturnType<AjaxCallbackSetup> {
        return {
            data: {
                className: "rp\\data\\event\\raid\\attendee\\EventRaidAttendeeAction",
            },
        };
    }
    
    _ajaxSuccess(data: AttendeeObjectActionResponse): void {
        if (data.actionName === "loadUpdateStatus") {
            const dialog = UiDialog.openStatic("attendeeUpdateStatusDialog", data.returnValues.template!, {
                title: Language.get("rp.event.raid.updateStatus"),
            });

            const submitButton = dialog.content.querySelector("[data-type=submit]") as HTMLButtonElement;
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

export = AttendeeInlineEditor;

interface AttendeeData {
    buttons: {
        delete: HTMLAnchorElement | null;
        updateStatus: HTMLAnchorElement | null;
    },
    element: HTMLElement | undefined;
}

interface AttendeeObjectActionResponse extends DatabaseObjectActionResponse {
    returnValues: {
        status: number;
        template?: string;
    }
}

interface ClipboardActionData {
    data: {
        actionName: string;
        internalData: {
            objectIDs: number[];
            template: string;
        };
    };
    responseData: ClipboardResponseData | null;
}

interface ClipboardResponseData {
    objectIDs: number[];
}

interface InlineEditorPermissions {
    canEdit: boolean;
}