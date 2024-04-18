import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { api } from '../Interfaces/enviroment';
import { Observable } from 'rxjs';
import { JuegoActivo } from '../Interfaces/juego-activo';
import { Movimientos } from '../Interfaces/movimientos';

@Injectable({
  providedIn: 'root'
})
export class JuegoService {

  constructor(private http: HttpClient) { }

  private getPartidaURL = `${api}/api/user/juego/get-game`
  private buscarPartidaURL = `${api}/api/user/buscar/partida`
  private mandarMisilURL = `${api}/api/user/juego/`
  private colocarBarcosURL = `${api}/api/user/juego/`

  getPartida(): Observable<JuegoActivo> {
    return this.http.post<JuegoActivo>(this.getPartidaURL, {})
  }

  buscarPartida(): Observable<JuegoActivo>{
    return this.http.post<JuegoActivo>(this.buscarPartidaURL, {})
  }

  colocarBarcos(barcos: [], id: Number) {
    return this.http.post(this.colocarBarcosURL + id + '/colocar-barcos', {barcos})
  }

  movimiento(horizontal: Number, vertical: Number, id: Number): Observable<Movimientos> {
    return this.http.post<Movimientos>(this.mandarMisilURL + id + 'hacer-movimiento', {horizontal, vertical});
  }

}
