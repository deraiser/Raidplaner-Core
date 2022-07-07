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
 * Drag and Drop attendee box's.
 *
 * @author      Marco Daries
 * @module      Daries/RP/Ui/Event/Raid/DragAndDrop/Box
 */

import * as Ajax from "WoltLabSuite/Core/Ajax";
import  { Autobind } from "./Autobind";
import * as Core from "WoltLabSuite/Core/Core";
import { DragTarget } from "./Data";
import * as DomUtil from "WoltLabSuite/Core/Dom/Util";
import * as UiNotification from "WoltLabSuite/Core/Ui/Notification";

class DragAndDropBox implements DragTarget {
    element: HTMLElement;
    
    constructor(element: HTMLElement) {
        this.element = element;
        
        this.configure();
    }
    
    protected configure(): void {
        this.element.addEventListener('dragover', this.dragOverHandler);
        this.element.addEventListener('drop', this.dropHandler);
        this.element.addEventListener('dragleave', this.dragLeaveHandler);
    }
    
    @Autobind
    dragOverHandler(event: DragEvent): void {
        if (!event.dataTransfer || event.dataTransfer.effectAllowed !== "move") return; 
        event.preventDefault();
        
        const droppable = <string>this.element.dataset.droppable;
        const droppableTo = <string>event.dataTransfer.getData("droppableTo");
        if (droppableTo.indexOf(droppable) < 0) return;
        
        this.element.classList.add("selected");
    }
    
    @Autobind
    dropHandler(event: DragEvent): void {
        if (!event.dataTransfer || event.dataTransfer.effectAllowed !== "move") return; 
        event.preventDefault();
        
        const droppable = <string>this.element.dataset.droppable;
        const droppableTo = <string>event.dataTransfer.getData("droppableTo");
        if (droppableTo.indexOf(droppable) < 0) return;
        
        const status = this.element.dataset.status;
        const distributionId = this.element.dataset.objectId;
        
        if (status === event.dataTransfer.getData("currentStatus") &&
            distributionId === event.dataTransfer.getData("distributionID")) return
        
        const attendeeId = <string>event.dataTransfer.getData("attendeeID");
        Ajax.apiOnce({
            data: {
                actionName: "updateStatus",
                className: "rp\\data\\event\\raid\\attendee\\EventRaidAttendeeAction",
                objectIDs: [ attendeeId ],
                parameters: {
                    distributionID: distributionId,
                    status: status,
                }
            },
            success: (data) => {
                const attendeeList = this.element.querySelector(".attendeeList") as HTMLElement;
                const attendee = document.getElementById(event.dataTransfer!.getData("id")) as HTMLElement;
                attendeeList.insertAdjacentElement("beforeend", attendee);
                
                UiNotification.show();
            },
        });
    }
    
    @Autobind
    dragLeaveHandler(event: DragEvent): void {
        if (!event.dataTransfer || event.dataTransfer.effectAllowed !== "move") return; 
        event.preventDefault();
        
        this.element.classList.remove("selected");
    }
}

Core.enableLegacyInheritance(DragAndDropBox);

export = DragAndDropBox;