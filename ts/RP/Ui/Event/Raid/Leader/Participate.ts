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
 * Manages the leader participate button in the raid event.
 *
 * @author      Marco Daries
 * @module      Daries/RP/Ui/Event/Raid/Leader/Participate
 */

import * as Core from "WoltLabSuite/Core/Core";
import * as DomChangeListener from "WoltLabSuite/Core/Dom/Change/Listener";
import * as DomUtil from "WoltLabSuite/Core/Dom/Util";
import FormBuilderDialog from "WoltLabSuite/Core/Form/Builder/Dialog";
import * as Language from "WoltLabSuite/Core/Language";
import { ParticipateAjaxResponse } from "../Participate/Data"
import * as UiNotification from "WoltLabSuite/Core/Ui/Notification";

class EventRaidParticipate {
    protected readonly _button: HTMLElement;
    protected _dialog: FormBuilderDialog;
    protected readonly _eventId: number;
    
    constructor(eventId: number) {
        this._eventId = eventId;
        
        this._button = document.querySelector(".jsButtonAttendeeAdd") as HTMLElement;
        this._button.addEventListener("click", () => this._click());
    }
    
    protected _ajaxSuccess(data: ParticipateAjaxResponse[]): void {
        data.forEach((participate: ParticipateAjaxResponse) => {
            document.querySelectorAll(".attendeeBox").forEach((attendeeBox: HTMLElement) => {
                if (participate.distributionId === ~~attendeeBox.dataset.objectId! &&
                    participate.status === ~~attendeeBox.dataset.status!) {
                    
                    const attendeeList = attendeeBox.querySelector(".attendeeList") as HTMLElement;
                    DomUtil.insertHtml(participate.template, attendeeList, "append");
                }
            });
        });

        DomChangeListener.trigger();
        UiNotification.show();
    }
    
    protected _getCharacterIds(): number[] {
        const characterIds: number[] = [];
        document.querySelectorAll(".attendee").forEach((attendee: HTMLElement) => {
            const characterId = ~~attendee.dataset.characterId!;
            if (characterId > 0) {
                characterIds.push(~~attendee.dataset.characterId!);
            }
        });
        return characterIds;
    }
    
    protected _click(): void {
        this._dialog = new FormBuilderDialog("addLeaderDialog", "rp\\data\\event\\raid\\attendee\\EventRaidAttendeeAction", "createLeaderAddDialog", {
            dialog: {
                title: Language.get("rp.event.raid.participate.add"),
            },
            actionParameters: {
                characterIDs: this._getCharacterIds(),
                eventID: this._eventId,
            },
            submitActionName: "submitLeaderAddDialog",
            successCallback:(data: ParticipateAjaxResponse[]) => this._ajaxSuccess(data),
            destroyOnClose: true,
        });

        this._dialog.open();
    }
}

Core.enableLegacyInheritance(EventRaidParticipate);

export = EventRaidParticipate;