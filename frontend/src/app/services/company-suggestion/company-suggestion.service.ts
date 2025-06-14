import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { map, Observable } from 'rxjs';
import { CompanySuggestion } from '../../models/company-suggestion';

@Injectable({
  providedIn: 'root'
})
export class CompanySuggestionService {

  private baseUrl = 'http://127.0.0.1:8000/api/companies/suggestions';

  constructor(private http: HttpClient) { }

 getSuggestions(metier: string): Observable<CompanySuggestion[]> {
  const body = { metier };

  return this.http.post<any>(this.baseUrl, body).pipe(
    // On transforme la réponse
    map(response => {
      const text = response.candidates?.[0]?.content?.parts?.[0]?.text;

      // Retirer les ```json ... ``` si présents
      const cleanedText = text?.replace(/```json\n?/, '').replace(/```/, '');

      return JSON.parse(cleanedText) as CompanySuggestion[];
    })
  );
}

}

