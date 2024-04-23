import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { environment } from '../../environments/environment.prod';
import { Observable } from 'rxjs';
import { User } from '../Interfaces/user-interface';

@Injectable({
  providedIn: 'root'
})
export class UserService {

  constructor(private http: HttpClient) { }

  private dataURL = `${environment.api}/api/user/me`
  private registroURL = `${environment.api}/api/user/registrobatalla/`

  getData(): Observable<User> {
    return this.http.get<User>(this.dataURL)
  }

  // getBatallas(): Observable<>

}
