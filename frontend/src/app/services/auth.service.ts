import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class AuthService {

  // Change le port et URL selon ta config Laravel, exemple souvent c'est 8000 en local
  private apiUrl = 'http://localhost:8000/api'; 

  constructor(private http: HttpClient) {}

  login(email: string, password: string): Observable<any> {
    const loginData = { email, password };

    const headers = new HttpHeaders({
      'Content-Type': 'application/json'
    });

    return this.http.post(`${this.apiUrl}/login`, loginData, { headers, responseType: 'json' });
  }

  registerUser(userData: any): Observable<any> {
    const headers = new HttpHeaders({
      'Content-Type': 'application/json'
    });

    return this.http.post(`${this.apiUrl}/register`, userData, { headers, responseType: 'text' as 'json' });
  }

  getToken(): string | null {
    return localStorage.getItem('authToken');
  }
}
