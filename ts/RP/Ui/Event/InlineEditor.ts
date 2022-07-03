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
 * Handles inline editing of events.
 *
 * @author      Marco Daries
 * @module      Daries/RP/Ui/Event/InlineEditor
 */

import * as Core from "WoltLabSuite/Core/Core";
import * as DomUtil from "WoltLabSuite/Core/Dom/Util";
import * as EventHandler from "WoltLabSuite/Core/Event/Handler";
import EventManager from "./Manager";
import * as Language from "WoltLabSuite/Core/Language";
import { DatabaseObjectActionResponse } from "WoltLabSuite/Core/Ajax/Data";
import * as UiConfirmation from "WoltLabSuite/Core/Ui/Confirmation";
import * as UiNotification from "WoltLabSuite/Core/Ui/Notification";

class UiEventInlineEditor {
    protected readonly _elements = new Map<string, HTMLElement>();
    protected readonly _eventId: number;
    protected readonly _eventManager: EventManager;
    
    constructor(eventId: number) {
        this._eventId = eventId;
        
        this._eventManager = new EventManager(eventId);
        
        this._show();
        
        EventHandler.add("Daries/RP/Ui/Event/Manager", "_ajaxSuccess", (data: DatabaseObjectActionResponse) => this._ajaxSuccess(data));
    }
    
    protected _click(element: HTMLElement, event: MouseEvent | null): void {
        if (event) {
            event.preventDefault();
        }
        
        const optionName = element.dataset.optionName!;
        
        if (optionName === "editLink") {
            window.location.href = element.dataset.link!;
        } else {
            this._execute(optionName);
        }
    }
    
    protected _execute(optionName: string): void {
        if (!this._validate(optionName)) {
            return;
        }
        
        switch(optionName) {
            case "disable":
            case "enable":
            case "restore":
                this._eventManager.update(this._eventId.toString(), optionName);
                break;
                
            case "delete":
                UiConfirmation.show({
                    confirm: () => {
                        this._eventManager.update(this._eventId.toString(), optionName);
                    },
                    message: Language.get("rp.event.delete.confirmMessage"),
                    messageIsHtml: true,
                });
                break;
                
            case "trash":
                UiConfirmation.show({
                    confirm: () => {
                        const textarea = UiConfirmation.getContentElement().querySelector("textarea") as HTMLTextAreaElement;
                        
                        this._eventManager.update(
                            this._eventId.toString(),
                            optionName,
                            {
                                data: {
                                    reason: textarea.value.trim()
                                }
                            }
                        );
                    },
                    message: Language.get("rp.event.trash.confirmMessage"),
                    messageIsHtml: true,
                    template: `
                        <div class="section">
                            <dl>
                                <dt>${Language.get("rp.event.trash.reason")}</dt>
                                <dd><textarea cols="40" rows="4"></textarea></dd>
                            </dl>
                        </div>`,
                });
                break;
        }
    }
    
    protected _show(): void {
        let hasShowElements = false;
        
        document.querySelectorAll(".jsEventDropdownItems > li").forEach((element: HTMLElement) => {
            const optionName = element.dataset.optionName;
            if (optionName) {
                if (this._validate(optionName)) {
                    DomUtil.show(element);
                    hasShowElements = true;
                } else {
                    DomUtil.hide(element);
                }
                
                if (!this._elements.get(optionName)) {
                    element.addEventListener("click", (ev) => this._click(element, ev));
                    
                    this._elements.set(
                        optionName,
                        element
                    );
                }
            }
        });
        
        if (!hasShowElements) {
            const eventDropdown = document.querySelector(".jsEventDropdown") as HTMLElement;
            eventDropdown.remove();
        }
    }
    
    protected _ajaxSuccess(data: DatabaseObjectActionResponse): void {
        switch(data.actionName) {
            case "disable":
            case "enable":
                this._eventManager.updateItems(
                    [this._eventId.toString()],
                    {
                        isDisabled: (data.actionName === "disable") ? "1" : "0"
                    }
                );
                break;
                
            case "restore":
            case "trash":
                this._eventManager.updateItems(
                    [this._eventId.toString()],
                    {
                        isDeleted: (data.actionName === "trash") ? "1" : "0"
                    }
                );
                break;
        }
        
        this._show();
        UiNotification.show();
    }
    
    protected _validate(optionName: string): boolean {
        const eventId = this._eventId.toString();
        
        switch(optionName) {
            case "delete":
                if (!this._eventManager.getPermission(eventId, "deleteEvent")) {
                    return false;
                }
                
                if (this._eventManager.getPropertyValue(eventId, "isDeleted", true)) {
                    return true;
                }
                break;
            
            case "restore":
                if (!this._eventManager.getPermission(eventId, "restoreEvent")) {
                    return false;
                }
                
                if (this._eventManager.getPropertyValue(eventId, "isDeleted", true)) {
                    return true;
                }
                break;
            
            case "trash":
                if (!this._eventManager.getPermission(eventId, "trashEvent")) {
                    return false;
                }
                
                if (!this._eventManager.getPropertyValue(eventId, "isDeleted", true)) {
                    return true;
                }
                break;
                
            case "enable":
            case "disable":
                if (!this._eventManager.getPermission(eventId, "moderateEvent")) {
                    return false;
                }
                
                if (this._eventManager.getPropertyValue(eventId, "isDeleted", true)) {
                    return false;
                }
                
                if (this._eventManager.getPropertyValue(eventId, "isDisabled", true)) {
                    return (optionName === "enable");
                } else {
                    return (optionName === "disable");
                }
                break;
                
            case "editLink":
                if (!this._eventManager.getPermission(eventId, "editEvent")) {
                    return false;
                }
                
                return true;
                break;
        }
        
        return false;
    }
}

Core.enableLegacyInheritance(UiEventInlineEditor);

export = UiEventInlineEditor;