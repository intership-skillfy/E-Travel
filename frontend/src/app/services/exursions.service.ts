// src/app/services/excursions.service.ts
import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root',
})
export class ExursionsService {
  private apiUrl = 'https://127.0.0.1:8000/api/offers/excursions'; // Replace with your API URL

  constructor(private http: HttpClient) {}

  getExcursions(): Observable<any[]> {
    return this.http.get<any[]>(this.apiUrl);
  }
}
export interface excursion {
  image: string;
  title: string;
  destination: string;
  categorie: string;
}