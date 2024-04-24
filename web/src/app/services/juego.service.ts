import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { environment } from '../../environments/environment.prod';
import { Observable } from 'rxjs';
import { JuegoActivo } from '../Interfaces/juego-activo';
import { Movimientos } from '../Interfaces/movimientos';

@Injectable({
  providedIn: 'root'
})
export class JuegoService {

  constructor(private http: HttpClient) { }

  private getPartidaURL = `${environment.api}/api/user/juego/get-game`
  private buscarPartidaURL = `${environment.api}/api/user/buscar/partida`
  private mandarMisilURL = `${environment.api}/api/user/juego/`
  private colocarBarcosURL = `${environment.api}/api/user/juego/`
  private getbarcosURL = `${environment.api}/api/user/getBarcosCount`
  private getQueueURL = `${environment.api}/api/user/get-queue`

  getPartida(): Observable<JuegoActivo> {
    return this.http.post<JuegoActivo>(this.getPartidaURL, {})
  }

  getQueue() {
    return this.http.get(this.getQueueURL)
  }


  buscarPartida(): Observable<JuegoActivo>{
    return this.http.post<JuegoActivo>(this.buscarPartidaURL, {})
  }

  colocarBarcos(barcos: [], id: Number) {
    return this.http.post(this.colocarBarcosURL + id + '/colocar-barcos', {ship_positions: barcos})
  }

  movimiento(horizontal: Number, vertical: Number, id: Number): Observable<Movimientos> {
    return this.http.post<Movimientos>(this.mandarMisilURL + id + '/hacer-movimiento', {horizontal, vertical});
  }

  getbarcos(){
    return this.http.get(this.getbarcosURL);
  }

}
