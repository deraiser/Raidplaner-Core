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
define(["require", "exports", "tslib", "WoltLabSuite/Core/Core", "WoltLabSuite/Core/Dom/Util", "WoltLabSuite/Core/Event/Handler", "./Manager", "WoltLabSuite/Core/Language", "WoltLabSuite/Core/Ui/Confirmation", "WoltLabSuite/Core/Ui/Notification"], function (require, exports, tslib_1, Core, DomUtil, EventHandler, Manager_1, Language, UiConfirmation, UiNotification) {
    "use strict";
    Core = tslib_1.__importStar(Core);
    DomUtil = tslib_1.__importStar(DomUtil);
    EventHandler = tslib_1.__importStar(EventHandler);
    Manager_1 = tslib_1.__importDefault(Manager_1);
    Language = tslib_1.__importStar(Language);
    UiConfirmation = tslib_1.__importStar(UiConfirmation);
    UiNotification = tslib_1.__importStar(UiNotification);
    class UiEventInlineEditor {
        constructor(eventId) {
            this._elements = new Map();
            this._eventId = eventId;
            this._eventManager = new Manager_1.default(eventId);
            this._show();
            EventHandler.add("Daries/RP/Ui/Event/Manager", "_ajaxSuccess", (data) => this._ajaxSuccess(data));
        }
        _click(element, event) {
            if (event) {
                event.preventDefault();
            }
            const optionName = element.dataset.optionName;
            if (optionName === "editLink" || optionName === 'transform') {
                window.location.href = element.dataset.link;
            }
            else {
                this._execute(optionName);
            }
        }
        _execute(optionName) {
            if (!this._validate(optionName)) {
                return;
            }
            switch (optionName) {
                case "disable":
                case "enable":
                case "restore":
                    this._eventManager.update(this._eventId.toString(), optionName);
                    break;
                case "cancel":
                    UiConfirmation.show({
                        confirm: () => {
                            this._eventManager.update(this._eventId.toString(), optionName);
                        },
                        message: Language.get("rp.event.raid.cancel.confirmMessage"),
                        messageIsHtml: true,
                    });
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
                            const textarea = UiConfirmation.getContentElement().querySelector("textarea");
                            this._eventManager.update(this._eventId.toString(), optionName, {
                                data: {
                                    reason: textarea.value.trim()
                                }
                            });
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
        _show() {
            let hasShowElements = false;
            document.querySelectorAll(".jsEventDropdownItems > li").forEach((element) => {
                const optionName = element.dataset.optionName;
                if (optionName) {
                    if (this._validate(optionName)) {
                        DomUtil.show(element);
                        hasShowElements = true;
                    }
                    else {
                        DomUtil.hide(element);
                    }
                    if (!this._elements.get(optionName)) {
                        element.addEventListener("click", (ev) => this._click(element, ev));
                        this._elements.set(optionName, element);
                        if (optionName === "editLink") {
                            const toggleButton = document.querySelector(".jsEventDropdown > .dropdownToggle");
                            toggleButton.addEventListener("dblclick", (event) => {
                                event.preventDefault();
                                if (!this._validate("editLink"))
                                    return;
                                element.click();
                            });
                        }
                    }
                }
            });
            const eventDropdown = document.querySelector(".jsEventDropdown");
            if (!hasShowElements) {
                eventDropdown.remove();
            }
            else {
                DomUtil.show(eventDropdown);
            }
        }
        _ajaxSuccess(data) {
            switch (data.actionName) {
                case "cancel":
                    window.location.reload();
                    break;
                case "disable":
                case "enable":
                    this._eventManager.updateItems([this._eventId.toString()], {
                        isDisabled: (data.actionName === "disable") ? "1" : "0"
                    });
                    break;
                case "restore":
                case "trash":
                    this._eventManager.updateItems([this._eventId.toString()], {
                        isDeleted: (data.actionName === "trash") ? "1" : "0"
                    });
                    break;
            }
            this._show();
            UiNotification.show();
        }
        _validate(optionName) {
            const eventId = this._eventId.toString();
            switch (optionName) {
                case "cancel":
                    if (!this._eventManager.getPermission(eventId, "cancelEvent")) {
                        return false;
                    }
                    if (!this._eventManager.getPropertyValue(eventId, "isCanceled", true)) {
                        return true;
                    }
                    break;
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
                    if (this._eventManager.getPropertyValue(eventId, "isCanceled", true)) {
                        return false;
                    }
                    if (this._eventManager.getPropertyValue(eventId, "isDeleted", true)) {
                        return false;
                    }
                    if (this._eventManager.getPropertyValue(eventId, "isDisabled", true)) {
                        return (optionName === "enable");
                    }
                    else {
                        return (optionName === "disable");
                    }
                    break;
                case "editLink":
                    if (!this._eventManager.getPermission(eventId, "editEvent")) {
                        return false;
                    }
                    return true;
                    break;
                case "transform":
                    if (!this._eventManager.getPermission(eventId, "transform")) {
                        return false;
                    }
                    return true;
                    break;
            }
            return false;
        }
    }
    Core.enableLegacyInheritance(UiEventInlineEditor);
    return UiEventInlineEditor;
});
