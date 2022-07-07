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
 * It creates an item to go on the list
 *
 * @author      Marco Daries
 * @package     Daries\RP
 * @module      Daries/RP/Ui/Event/Raid/DragAndDrop/Item
 */

import { Autobind } from "./Autobind";
import * as Core from "WoltLabSuite/Core/Core";
import { Draggable } from "./Data";
import * as DomUtil from "WoltLabSuite/Core/Dom/Util";

class DragAndDropItem implements Draggable {
    element: HTMLElement;
    
    constructor(element: HTMLElement) {
        this.element = element;
        
        this.configure();
    }
    
    protected configure(): void {
        this.element.addEventListener('dragstart', this.dragStartHandler);
        this.element.addEventListener('dragend', this.dragEndHandler);
    }
    
    @Autobind
    dragEndHandler(_: DragEvent): void {
        document.querySelectorAll(".attendeeBox").forEach((attendeeBox: HTMLElement) => {
            attendeeBox.classList.remove("droppable");
            attendeeBox.classList.remove("selected");
        });
    }
    
    @Autobind
    dragStartHandler(event: DragEvent): void {
        const attendee = event.target as HTMLElement;
        
        if (attendee.classList.contains("attendee")) {
            event.dataTransfer!.setData("id", attendee.id);
            event.dataTransfer!.setData("attendeeID", attendee.dataset.objectId!);
            event.dataTransfer!.setData("droppableTo", attendee.dataset.droppableTo!);
            event.dataTransfer!.effectAllowed = 'move';
            
            const currentBox = attendee.closest(".attendeeBox") as HTMLElement;
            event.dataTransfer!.setData("currentStatus", currentBox.dataset.status!);
            event.dataTransfer!.setData("distributionID", currentBox.dataset.objectId!);
            
            document.querySelectorAll(".attendeeBox").forEach((attendeeBox: HTMLElement) => {
                const droppable = <string>attendeeBox.dataset.droppable;
                const droppableTo = <string>attendee.dataset.droppableTo;
                if (droppableTo.indexOf(droppable) < 0) return;
                
                attendeeBox.classList.add("droppable");
            });
        }
    }
}

Core.enableLegacyInheritance(DragAndDropItem);

export = DragAndDropItem;