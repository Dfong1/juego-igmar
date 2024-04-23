import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { environment } from '../../environments/environment.prod';

import { Observable } from 'rxjs';
import { Message } from '../Interfaces/message';

@Injectable({
  providedIn: 'root'
})
export class RegisterService {

  constructor(private http: HttpClient) { }

  private registerURL = `${environment.api}/api/auth/register`

  register( name: string, email:string, password:string  ): Observable<Message>{
    return this.http.post<Message>(this.registerURL, { name: name, email: email, password: password })
  }

  

}
