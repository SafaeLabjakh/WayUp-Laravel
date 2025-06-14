import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class QuestionService {

  private apiUrl = 'http://localhost:8000/api'; // URL Laravel (ajuste si besoin)

  constructor(private http: HttpClient) {}

  // Récupérer les compétences d’un utilisateur
  getSkills(userId: number): Observable<any[]> {
    const token = localStorage.getItem('authToken');
    const headers = new HttpHeaders({
      Authorization: `Bearer ${token || ''}`
    });
    return this.http.get<any[]>(`${this.apiUrl}/skills/${userId}`, { headers });
  }

  // Mettre à jour une compétence (exemple : champ acquired)
  updateSkill(skillId: number, acquired: boolean): Observable<any> {
    const token = localStorage.getItem('authToken');
    const headers = new HttpHeaders({
      Authorization: `Bearer ${token || ''}`,
      'Content-Type': 'application/json'
    });
    const body = { acquired: acquired };
    return this.http.put<any>(`${this.apiUrl}/skills/${skillId}`, body, { headers });
  }

  // Soumettre un test pour générer un métier (appel à Laravel /resultat/{userId})
  submitTest(userId: number, testData: any): Observable<any> {
    const token = localStorage.getItem('authToken');
    const headers = new HttpHeaders({
      Authorization: `Bearer ${token || ''}`,
      'Content-Type': 'application/json'
    });

    return this.http.post(`${this.apiUrl}/resultat/${userId}`, testData, {
      headers: headers
    });
  }

  getQuestions(): Observable<any[]> {
  const token = localStorage.getItem('authToken');
  const headers = new HttpHeaders({
    Authorization: `Bearer ${token || ''}`
  });
  return this.http.get<any[]>(`${this.apiUrl}/questions`, { headers });
}

}
