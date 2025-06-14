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
    map(response => {
      const rawText = response?.candidates?.[0]?.content?.parts?.[0]?.text;

      if (!rawText) {
        throw new Error("La réponse ne contient pas de texte.");
      }

      console.log("Raw API response text:", rawText);

      // Extraire uniquement la partie JSON dans le bloc markdown
const jsonMatch = rawText.match(/```json\s*([\s\S]*?)\s*```/i);
      if (!jsonMatch || jsonMatch.length < 2) {
        throw new Error("Impossible d'extraire le JSON du texte.");
      }

      const jsonText = jsonMatch[1].trim();

      try {
        return JSON.parse(jsonText) as CompanySuggestion[];
      } catch (error) {
        console.error("Erreur lors du parsing JSON :", error);
        throw new Error("Le contenu retourné n'est pas un JSON valide.");
      }
    })
  );
}

}