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
define(["require", "exports", "tslib", "./Autobind", "WoltLabSuite/Core/Core"], function (require, exports, tslib_1, Autobind_1, Core) {
    "use strict";
    Core = tslib_1.__importStar(Core);
    class DragAndDropItem {
        constructor(element) {
            this.element = element;
            this.configure();
        }
        configure() {
            this.element.addEventListener('dragstart', this.dragStartHandler);
            this.element.addEventListener('dragend', this.dragEndHandler);
        }
        dragEndHandler(_) {
            document.querySelectorAll(".attendeeBox").forEach((attendeeBox) => {
                attendeeBox.classList.remove("droppable");
                attendeeBox.classList.remove("selected");
            });
        }
        dragStartHandler(event) {
            const attendee = event.target;
            if (attendee.classList.contains("attendee")) {
                event.dataTransfer.setData("id", attendee.id);
                event.dataTransfer.setData("attendeeID", attendee.dataset.objectId);
                event.dataTransfer.setData("droppableTo", attendee.dataset.droppableTo);
                event.dataTransfer.effectAllowed = 'move';
                const currentBox = attendee.closest(".attendeeBox");
                event.dataTransfer.setData("currentStatus", currentBox.dataset.status);
                event.dataTransfer.setData("distributionID", currentBox.dataset.objectId);
                document.querySelectorAll(".attendeeBox").forEach((attendeeBox) => {
                    const droppable = attendeeBox.dataset.droppable;
                    const droppableTo = attendee.dataset.droppableTo;
                    if (droppableTo.indexOf(droppable) < 0)
                        return;
                    attendeeBox.classList.add("droppable");
                });
            }
        }
    }
    tslib_1.__decorate([
        Autobind_1.Autobind
    ], DragAndDropItem.prototype, "dragEndHandler", null);
    tslib_1.__decorate([
        Autobind_1.Autobind
    ], DragAndDropItem.prototype, "dragStartHandler", null);
    Core.enableLegacyInheritance(DragAndDropItem);
    return DragAndDropItem;
});
