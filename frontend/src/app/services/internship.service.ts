import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable, throwError } from 'rxjs';
import { catchError } from 'rxjs/operators';
import { InternshipSuggestion } from '../models/internship-suggestion.model';

@Injectable({
  providedIn: 'root'
})
export class InternshipService {

  // URL de base de l'API Laravel (ajuste le port si besoin)
  private baseUrl = 'http://localhost:8000/api/internships';  
  private userInternshipsUrl = `${this.baseUrl}/me`;

  constructor(private http: HttpClient) {}

  // Récupère la liste des stages de l'utilisateur connecté
  getUserInternships(): Observable<InternshipSuggestion[]> {
    const token = localStorage.getItem('authToken');

    if (!token) {
      console.error('Token manquant.');
      return throwError(() => new Error('Token manquant. Veuillez vous connecter.'));
    }

    const headers = new HttpHeaders({
      'Content-Type': 'application/json',
      'Authorization': `Bearer ${token}`
    });

    return this.http.get<InternshipSuggestion[]>(this.userInternshipsUrl, { headers })
      .pipe(
        catchError(error => {
          console.error('Erreur lors de la récupération des stages:', error);
          let errorMessage = 'Erreur de chargement des stages.';

          if (error.status === 401) {
            errorMessage = 'Session expirée. Veuillez vous reconnecter.';
          } else if (error.status === 400) {
            errorMessage = error.error?.message || 'Préférences de poste non définies.';
          } else if (error.status === 404) {
            errorMessage = 'Utilisateur introuvable.';
          }

          return throwError(() => new Error(errorMessage));
        })
      );
  }

  /**
   * Méthode pour tester l’API Gemini via le backend
   * @param jobTitle Titre du poste pour rechercher des stages
   * @returns Observable de la réponse brute (à typer selon ce que renvoie l’API)
   */
  testInternshipApi(jobTitle: string): Observable<any> {
    const token = localStorage.getItem('authToken');

    if (!token) {
      return throwError(() => new Error('Token manquant. Veuillez vous connecter.'));
    }

    const headers = new HttpHeaders({
      'Content-Type': 'application/json',
      'Authorization': `Bearer ${token}`
    });

    // Ici, j’imagine que tu as une route Laravel du type :
    // GET /api/internships/test-internship-api/{jobTitle}
    // qui fait appel à ta fonction de test avec Gemini.
    const url = `${this.baseUrl}/test-internship-api/${encodeURIComponent(jobTitle)}`;

    return this.http.get(url, { headers })
      .pipe(
        catchError(error => {
          console.error('Erreur lors du test de l’API Gemini:', error);
          return throwError(() => new Error('Erreur lors du test de l’API Gemini.'));
        })
      );
  }
}
