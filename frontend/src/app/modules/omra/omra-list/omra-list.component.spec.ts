import { ComponentFixture, TestBed } from '@angular/core/testing';

import { OmraListComponent } from './omra-list.component';

describe('OmraListComponent', () => {
  let component: OmraListComponent;
  let fixture: ComponentFixture<OmraListComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [OmraListComponent]
    });
    fixture = TestBed.createComponent(OmraListComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
