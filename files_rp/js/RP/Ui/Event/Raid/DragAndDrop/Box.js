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
define(["require", "exports", "tslib", "WoltLabSuite/Core/Ajax", "./Autobind", "WoltLabSuite/Core/Core", "WoltLabSuite/Core/Ui/Notification"], function (require, exports, tslib_1, Ajax, Autobind_1, Core, UiNotification) {
    "use strict";
    Ajax = tslib_1.__importStar(Ajax);
    Core = tslib_1.__importStar(Core);
    UiNotification = tslib_1.__importStar(UiNotification);
    class DragAndDropBox {
        constructor(element) {
            this.element = element;
            this.configure();
        }
        configure() {
            this.element.addEventListener('dragover', this.dragOverHandler);
            this.element.addEventListener('drop', this.dropHandler);
            this.element.addEventListener('dragleave', this.dragLeaveHandler);
        }
        dragOverHandler(event) {
            if (!event.dataTransfer || event.dataTransfer.effectAllowed !== "move")
                return;
            event.preventDefault();
            const droppable = this.element.dataset.droppable;
            const droppableTo = event.dataTransfer.getData("droppableTo");
            if (droppableTo.indexOf(droppable) < 0)
                return;
            this.element.classList.add("selected");
        }
        dropHandler(event) {
            if (!event.dataTransfer || event.dataTransfer.effectAllowed !== "move")
                return;
            event.preventDefault();
            const droppable = this.element.dataset.droppable;
            const droppableTo = event.dataTransfer.getData("droppableTo");
            if (droppableTo.indexOf(droppable) < 0)
                return;
            const status = this.element.dataset.status;
            const distributionId = this.element.dataset.objectId;
            if (status === event.dataTransfer.getData("currentStatus") &&
                distributionId === event.dataTransfer.getData("distributionID"))
                return;
            const attendeeId = ~~event.dataTransfer.getData("attendeeID");
            Ajax.apiOnce({
                data: {
                    actionName: "updateStatus",
                    className: "rp\\data\\event\\raid\\attendee\\EventRaidAttendeeAction",
                    objectIDs: [attendeeId],
                    parameters: {
                        distributionID: distributionId,
                        status: status,
                    }
                },
                success: () => {
                    const attendeeList = this.element.querySelector(".attendeeList");
                    const attendee = document.getElementById(event.dataTransfer.getData("id"));
                    attendeeList.insertAdjacentElement("beforeend", attendee);
                    UiNotification.show();
                },
            });
        }
        dragLeaveHandler(event) {
            if (!event.dataTransfer || event.dataTransfer.effectAllowed !== "move")
                return;
            event.preventDefault();
            this.element.classList.remove("selected");
        }
    }
    tslib_1.__decorate([
        Autobind_1.Autobind
    ], DragAndDropBox.prototype, "dragOverHandler", null);
    tslib_1.__decorate([
        Autobind_1.Autobind
    ], DragAndDropBox.prototype, "dropHandler", null);
    tslib_1.__decorate([
        Autobind_1.Autobind
    ], DragAndDropBox.prototype, "dragLeaveHandler", null);
    Core.enableLegacyInheritance(DragAndDropBox);
    return DragAndDropBox;
});
