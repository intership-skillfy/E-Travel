import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ExcursionDetailsComponent } from './excursion-details.component';

describe('ExcursionDetailsComponent', () => {
  let component: ExcursionDetailsComponent;
  let fixture: ComponentFixture<ExcursionDetailsComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [ExcursionDetailsComponent]
    });
    fixture = TestBed.createComponent(ExcursionDetailsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
