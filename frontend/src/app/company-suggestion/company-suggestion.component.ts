import { CommonModule } from '@angular/common';
import { HttpClientModule } from '@angular/common/http';
import { Component, inject, OnInit } from '@angular/core';
import { CompanySuggestionService } from '../services/company-suggestion/company-suggestion.service';
import { CompanySuggestion } from '../models/company-suggestion';
import { SidebarComponent } from "../sidebar/sidebar.component";

@Component({
  selector: 'app-company-suggestion',
  standalone: true,
  imports: [CommonModule, SidebarComponent],
  templateUrl: './company-suggestion.component.html',
  styleUrl: './company-suggestion.component.css'
})
export class CompanySuggestionComponent implements OnInit 
 {
   private service = inject(CompanySuggestionService);

  companies: CompanySuggestion[] = [];
  userId!: number;

  ngOnInit() {
  const userData = localStorage.getItem('user');
  if (userData) {
    const parsedUser = JSON.parse(userData);
    this.userId = parsedUser.id;

    const metier = parsedUser.metierSugg || null;

    if (metier) {
      this.service.getSuggestions(metier).subscribe({
        next: (data) => {
          this.companies = data;
          console.log("data", this.companies);
        },
        error: (err) => console.error('Error fetching companies', err),
      });
    } else {
      console.error('No metierSugg found in user data');
    }

  } else {
    console.error('No user data found in localStorage');
  }
}}
