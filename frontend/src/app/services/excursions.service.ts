import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { CorsRequest } from 'cors';

@Injectable({
  providedIn: 'root',
  
})
export class ExcursionsService {
  private apiUrl = 'http://127.0.0.1:8000/api/offres'; 

  constructor(private http: HttpClient) {}

  getOffers(): Observable<any[]> {
    return this.http.get<any[]>(this.apiUrl);
  }
}

export interface Excursion {
  banner: string;
  name: string;
  destination: string;
  categories: string;
}
