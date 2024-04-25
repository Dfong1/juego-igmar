import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { environment } from '../../environments/environment.prod';
import { Observable } from 'rxjs';
import { Estadistica } from '../Interfaces/estadistica';

@Injectable({
  providedIn: 'root'
})
export class EstadisticaService {

  constructor(private http: HttpClient) { }

  private getEstadisticaURL = `${environment.api}/api/user/registrobatalla`

  getEstadistica(): Observable<Estadistica>{
    return this.http.get<Estadistica>(this.getEstadisticaURL)
  }

}
