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
define(["require", "exports", "tslib", "WoltLabSuite/Core/Core", "WoltLabSuite/Core/Dom/Change/Listener", "WoltLabSuite/Core/Dom/Util", "WoltLabSuite/Core/Form/Builder/Dialog", "WoltLabSuite/Core/Language", "WoltLabSuite/Core/Ui/Notification"], function (require, exports, tslib_1, Core, DomChangeListener, DomUtil, Dialog_1, Language, UiNotification) {
    "use strict";
    Core = tslib_1.__importStar(Core);
    DomChangeListener = tslib_1.__importStar(DomChangeListener);
    DomUtil = tslib_1.__importStar(DomUtil);
    Dialog_1 = tslib_1.__importDefault(Dialog_1);
    Language = tslib_1.__importStar(Language);
    UiNotification = tslib_1.__importStar(UiNotification);
    class EventRaidParticipate {
        constructor(eventId) {
            this._eventId = eventId;
            this._button = document.querySelector(".jsButtonAttendeeAdd");
            this._button.addEventListener("click", () => this._click());
        }
        _ajaxSuccess(data) {
            data.forEach((participate) => {
                document.querySelectorAll(".attendeeBox").forEach((attendeeBox) => {
                    if (participate.distributionId === ~~attendeeBox.dataset.objectId &&
                        participate.status === ~~attendeeBox.dataset.status) {
                        const attendeeList = attendeeBox.querySelector(".attendeeList");
                        DomUtil.insertHtml(participate.template, attendeeList, "append");
                    }
                });
            });
            DomChangeListener.trigger();
            UiNotification.show();
        }
        _getCharacterIds() {
            const characterIds = [];
            document.querySelectorAll(".attendee").forEach((attendee) => {
                const characterId = ~~attendee.dataset.characterId;
                if (characterId > 0) {
                    characterIds.push(~~attendee.dataset.characterId);
                }
            });
            return characterIds;
        }
        _click() {
            this._dialog = new Dialog_1.default("addLeaderDialog", "rp\\data\\event\\raid\\attendee\\EventRaidAttendeeAction", "createLeaderAddDialog", {
                dialog: {
                    title: Language.get("rp.event.raid.participate.add"),
                },
                actionParameters: {
                    characterIDs: this._getCharacterIds(),
                    eventID: this._eventId,
                },
                submitActionName: "submitLeaderAddDialog",
                successCallback: (data) => this._ajaxSuccess(data),
                destroyOnClose: true,
            });
            this._dialog.open();
        }
    }
    Core.enableLegacyInheritance(EventRaidParticipate);
    return EventRaidParticipate;
});
