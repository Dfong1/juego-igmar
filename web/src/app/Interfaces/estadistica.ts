export interface Estadistica {

    msg: string;
    data: [{
        user_id: number;
        rival_id: number;
        partida: number;
        user_name: string;
        rival_name: string;
        user_ships_remaining: number
        rival_ships_remaining: number
    }]

}
