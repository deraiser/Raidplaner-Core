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
 * @author      Marco Daries
 * @module      Daries/RP/Ui/Event/Manager
 */

import * as Core from "WoltLabSuite/Core/Core"; 
import * as EventHandler from "WoltLabSuite/Core/Event/Handler";
import Manager from "WoltLabSuite/Core/Ui/Message/Manager";
import { DatabaseObjectActionResponse } from "WoltLabSuite/Core/Ajax/Data";

class UiEventManager extends Manager {
    protected readonly eventId: number;
    
    constructor(eventId: number) {
        super({
            className: "rp\\data\\event\\EventAction",
            selector: ".rpEventHeader",
        });
        
        this.eventId = eventId;
    }
    
    _ajaxSuccess(_data: DatabaseObjectActionResponse): void {
        EventHandler.fire("Daries/RP/Ui/Event/Manager", "_ajaxSuccess", _data);
    }
}

Core.enableLegacyInheritance(UiEventManager);

export = UiEventManager;