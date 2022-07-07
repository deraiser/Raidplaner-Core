import { DatabaseObjectActionResponse } from "WoltLabSuite/Core/Ajax/Data";

export interface AttendeeData {
    buttons: {
        delete: HTMLAnchorElement | null;
        updateStatus: HTMLAnchorElement | null;
    },
    element: HTMLElement | undefined;
}

export interface AttendeeObjectActionResponse extends DatabaseObjectActionResponse {
    returnValues: {
        status: number;
        template?: string;
    }
}

export interface ClipboardActionData {
    data: {
        actionName: string;
        internalData: {
            objectIDs: number[];
            template: string;
        };
    };
    responseData: ClipboardResponseData | null;
}

export interface ClipboardResponseData {
    objectIDs: number[];
}

export interface InlineEditorPermissions {
    canEdit: boolean;
}