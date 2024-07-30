import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ExursionsComponent } from '../exursions/exursions.component';
import { ExursionsListComponent } from './exursions-list/exursions-list.component';
import { NgApexchartsModule } from 'ng-apexcharts';
import { InlineSVGModule } from 'ng-inline-svg-2';




@NgModule({
  declarations: [
    ExursionsListComponent,
    ExursionsComponent


  ],
  imports: [
    CommonModule,
    NgApexchartsModule,
    InlineSVGModule,
    


  ],
  exports: [ExursionsComponent,

  ]  

})
export class ExursionsModule { }
