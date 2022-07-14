export interface ParticipateAjaxResponse {
    attendeeId: number;
    distributionId: number;
    status: number;
    template: string;
}

export interface ParticipateButtonOptions {
    attendeeId: number;
    canParticipate: boolean;
    hasAttendee: boolean;
    isExpired: boolean;
}