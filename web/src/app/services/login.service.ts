import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { User } from '../Interfaces/user-interface';
import { api } from '../Interfaces/enviroment';

@Injectable({
  providedIn: 'root'
})

export class LoginService {
  private loginURL = `${api}/api/auth/login`;
  private token: string|null = null;
  private static instance: LoginService

  constructor(private http: HttpClient) {
    LoginService.instance = this;
  }

  public static getInstance(): LoginService{
    return LoginService.instance
  }
  
  login(email: string, password: string): Observable<any> {
    return this.http.post<any>(this.loginURL, { email, password });
  }

  setToken(token: string|null){
    this.token = token
  }
  getToken(): string|null{
    return this.token
  }

  
  LogIn(email: string, password: string): Observable<User> {
    return this.http.post<User>(this.loginURL, { email, password })
  }

  Verificar(): Observable<any> {
    let url = `${api}/api/auth/me`
    return this.http.post<any>(url, null)
  }
  
  verificarToken(token: string): Observable<any> {
    const url = `${api}/api/auth/verify`;
    return this.http.post<any>(url, { token });
  }
  

}
