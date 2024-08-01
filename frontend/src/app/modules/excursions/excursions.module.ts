import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ExcursionsComponent } from './excursions.component';
import { ExcursionsListComponent } from './excursions-list/excursions-list.component';
import { NgApexchartsModule } from 'ng-apexcharts';
import { InlineSVGModule } from 'ng-inline-svg-2';
import { RouterModule, Routes } from '@angular/router';
import { OffersRoutingModule } from 'src/app/pages/offers/offers-routing.module';




@NgModule({
  declarations: [
    ExcursionsListComponent,
    ExcursionsComponent


  ],
  imports: [
    CommonModule,
    NgApexchartsModule,
    InlineSVGModule,
    RouterModule,
    OffersRoutingModule
    


  ],
  exports: [ExcursionsComponent,

  ]  

})
export class ExcursionsModule { }
