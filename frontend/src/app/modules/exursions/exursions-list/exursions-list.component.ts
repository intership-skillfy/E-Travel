import { Component, OnInit } from '@angular/core';
import { ExursionsService } from 'src/app/services/exursions.service';


@Component({
  selector: 'app-exursions-list',
  templateUrl: './exursions-list.component.html',
  styleUrls: ['./exursions-list.component.scss'],
})
export class ExursionsListComponent implements OnInit{
  exursions: any[] = [];

  constructor(private exursionsService: ExursionsService) {}

  ngOnInit(): void {
    this.exursionsService.getExcursions().subscribe(
      (data) => {
        this.exursions = data;
      },
      (error) => {
        console.error('Error fetching excursions', error);
      }
    );
  }
}
