import { Component, OnInit } from '@angular/core';
import { ExcursionsService } from 'src/app/services/excursions.service';


@Component({
  selector: 'app-excursions-list',
  templateUrl: './excursions-list.component.html',
  styleUrls: ['./excursions-list.component.scss'],
})
export class ExcursionsListComponent implements OnInit {
  excursions: Excursion[] = [];
  error: any;

  constructor(private excursionsService: ExcursionsService) {}

  ngOnInit(): void {
    this.excursionsService.getOffers().subscribe(
      (data) => {
        console.log('API Response:', data); // Log the data to inspect it
        this.excursions = data.filter((offer) => offer.type === 'Excursion');
      },
      (err) => {
        console.error('Error fetching excursions:', err);
        this.error = err; // Store error for potential display
      }
    );
  }
}

export interface Excursion {
  banner: string;
  name: string;
  destination: string;
  categories: string;
  type: string; // Make sure to include all fields expected from the API
}
